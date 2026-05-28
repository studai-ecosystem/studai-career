<?php

declare(strict_types=1);

namespace App\Services\ResponsibleAI;

use App\Models\AIBiasReport;
use App\Models\AIDecisionLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BiasDetectionService
 *
 * Detects potential bias in AI scoring patterns.
 * Provides both real-time quick-scan and periodic aggregate analysis.
 */
class BiasDetectionService
{
    // Thresholds for flagging
    private const DISPARITY_FLAG_THRESHOLD  = 0.80; // 80% rule (EEOC guideline)
    private const SCORE_DIFF_FLAG_THRESHOLD = 10.0; // >10 point avg gap
    private const MIN_SAMPLE_SIZE           = 10;   // minimum decisions to analyse

    /**
     * Quick real-time bias scan on a single AI payload.
     * Called by ExplainableAIService before recording every decision.
     *
     * @param  array  $payload  {score, factors, input_context, recommendation}
     * @return array  {flagged: bool, indicators: array}
     */
    public function quickScan(array $payload): array
    {
        $indicators = [];

        $score   = (float) ($payload['score'] ?? 0);
        $context = $payload['input_context'] ?? [];
        $factors = $payload['factors'] ?? [];

        // ── Rule 1: Extreme score with no strong factors ───────────────────────
        $avgFactor = ! empty($factors)
            ? array_sum(array_column($factors, 'value')) / count($factors)
            : null;

        if ($score < 40 && $avgFactor !== null && $avgFactor > 60) {
            $indicators[] = [
                'type'     => 'score_factor_mismatch',
                'severity' => 'moderate',
                'detail'   => "Score ({$score}%) is much lower than average factor value ({$avgFactor}%). Review weighting.",
            ];
        }

        // ── Rule 2: High confidence + low score (potential bias signal) ────────
        $confidence = (float) ($payload['confidence'] ?? 0);
        if ($score < 35 && $confidence > 0.90) {
            $indicators[] = [
                'type'     => 'high_confidence_low_score',
                'severity' => 'low',
                'detail'   => 'Very high AI confidence on a very low score — ensure no protected attribute is driving this.',
            ];
        }

        // ── Rule 3: Recommendation doesn't match score range ──────────────────
        $recommendation = $payload['recommendation'] ?? null;
        if ($recommendation === 'reject' && $score >= 70) {
            $indicators[] = [
                'type'     => 'reject_high_score',
                'severity' => 'moderate',
                'detail'   => "AI recommends rejection but score is {$score}%. Human review required.",
            ];
        }

        if ($recommendation === 'shortlist' && $score < 40) {
            $indicators[] = [
                'type'     => 'shortlist_low_score',
                'severity' => 'low',
                'detail'   => "AI recommends shortlisting but score is only {$score}%. Verify context.",
            ];
        }

        return [
            'flagged'    => ! empty($indicators),
            'indicators' => $indicators,
        ];
    }

    /**
     * Run a full aggregate bias analysis for a given job/company scope.
     * Called periodically (e.g., daily via scheduled command).
     *
     * @param  string  $scope   'company' | 'job' | 'global'
     * @param  int|null $scopeId  Company or Job ID
     * @param  int     $days    Analysis window in days
     */
    public function runAggregateAnalysis(string $scope, ?int $scopeId, int $days = 30): AIBiasReport
    {
        $periodEnd   = Carbon::today();
        $periodStart = $periodEnd->copy()->subDays($days);

        $logs = $this->fetchDecisionLogs($scope, $scopeId, $periodStart, $periodEnd);

        $groupMetrics    = $this->analyseGroups($logs);
        $disparityRatios = $this->calculateDisparityRatios($groupMetrics);
        $biasLevel       = $this->determineBiasLevel($disparityRatios);
        $biasSeverity    = $this->calculateSeverityScore($disparityRatios);

        $protectedAttributes = $this->identifyAffectedAttributes($disparityRatios);
        $recommendations     = $this->buildRecommendations($biasLevel, $disparityRatios);

        $report = AIBiasReport::create([
            'report_type'                   => AIBiasReport::TYPE_DEMOGRAPHIC,
            'scope'                         => $scope,
            'scope_id'                      => $scopeId,
            'period_start'                  => $periodStart,
            'period_end'                    => $periodEnd,
            'total_decisions_analysed'      => $logs->count(),
            'group_metrics'                 => $groupMetrics,
            'disparity_ratios'              => $disparityRatios,
            'bias_severity'                 => $biasSeverity,
            'bias_level'                    => $biasLevel,
            'protected_attributes_affected' => $protectedAttributes,
            'recommendations'               => $recommendations,
            'requires_review'               => in_array($biasLevel, [AIBiasReport::LEVEL_HIGH, AIBiasReport::LEVEL_CRITICAL], true),
            'status'                        => 'pending',
        ]);

        Log::info('BiasDetectionService: aggregate analysis complete', [
            'scope'         => $scope,
            'scope_id'      => $scopeId,
            'total_logs'    => $logs->count(),
            'bias_level'    => $biasLevel,
            'bias_severity' => $biasSeverity,
        ]);

        return $report;
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function fetchDecisionLogs(
        string $scope,
        ?int $scopeId,
        Carbon $start,
        Carbon $end
    ): Collection {
        $query = AIDecisionLog::whereBetween('created_at', [$start, $end])
            ->whereNotNull('ai_score');

        // Scope filtering through input_context JSON
        if ($scope === 'job' && $scopeId) {
            $query->whereJsonContains('input_context->job_id', $scopeId);
        } elseif ($scope === 'company' && $scopeId) {
            $query->whereJsonContains('input_context->company_id', $scopeId);
        }

        return $query->get();
    }

    private function analyseGroups(Collection $logs): array
    {
        // Since we don't store demographics directly (to protect privacy),
        // we analyse score distributions by acceptance_rate proxies.
        // Groups are inferred from input_context where available.

        $total      = $logs->count();
        $shortlisted = $logs->where('ai_recommendation', 'shortlist')->count();
        $rejected    = $logs->where('ai_recommendation', 'reject')->count();
        $overridden  = $logs->where('was_overridden', true)->count();

        $avgScore = $total > 0 ? round($logs->avg('ai_score'), 2) : 0;

        return [
            'overview' => [
                'total'               => $total,
                'avg_score'           => $avgScore,
                'shortlist_count'     => $shortlisted,
                'shortlist_rate'      => $total > 0 ? round($shortlisted / $total * 100, 1) : 0,
                'reject_count'        => $rejected,
                'reject_rate'         => $total > 0 ? round($rejected / $total * 100, 1) : 0,
                'override_count'      => $overridden,
                'override_rate'       => $total > 0 ? round($overridden / $total * 100, 1) : 0,
                'bias_flagged_count'  => $logs->where('bias_flagged', true)->count(),
            ],
            'score_distribution' => [
                'p90' => $this->percentile($logs->pluck('ai_score')->toArray(), 90),
                'p75' => $this->percentile($logs->pluck('ai_score')->toArray(), 75),
                'p50' => $this->percentile($logs->pluck('ai_score')->toArray(), 50),
                'p25' => $this->percentile($logs->pluck('ai_score')->toArray(), 25),
                'p10' => $this->percentile($logs->pluck('ai_score')->toArray(), 10),
            ],
        ];
    }

    private function calculateDisparityRatios(array $groupMetrics): array
    {
        $overview  = $groupMetrics['overview'] ?? [];
        $overrideRate = $overview['override_rate'] ?? 0;
        $biasFlagged  = $overview['bias_flagged_count'] ?? 0;
        $total        = $overview['total'] ?? 1;

        $biasSignalRate = $total > 0 ? $biasFlagged / $total : 0;

        return [
            'override_rate'          => $overrideRate,
            'bias_signal_rate'       => round($biasSignalRate, 4),
            'bias_signal_percent'    => round($biasSignalRate * 100, 1),
            'disparity_flag_threshold' => self::DISPARITY_FLAG_THRESHOLD,
        ];
    }

    private function determineBiasLevel(array $ratios): string
    {
        $signalRate = $ratios['bias_signal_rate'] ?? 0;

        return match (true) {
            $signalRate >= 0.25 => AIBiasReport::LEVEL_CRITICAL,
            $signalRate >= 0.15 => AIBiasReport::LEVEL_HIGH,
            $signalRate >= 0.08 => AIBiasReport::LEVEL_MODERATE,
            $signalRate >= 0.03 => AIBiasReport::LEVEL_LOW,
            default             => AIBiasReport::LEVEL_NONE,
        };
    }

    private function calculateSeverityScore(array $ratios): float
    {
        // Normalise bias signal rate to 0.0-1.0
        return min(1.0, round(($ratios['bias_signal_rate'] ?? 0) * 4, 3));
    }

    private function identifyAffectedAttributes(array $ratios): array
    {
        $affected = [];
        $signalRate = $ratios['bias_signal_rate'] ?? 0;

        // Flag if override rate is high (suggests systemic AI error)
        if (($ratios['override_rate'] ?? 0) > 20) {
            $affected[] = 'general_ai_accuracy';
        }

        if ($signalRate > 0.08) {
            $affected[] = 'potential_demographic_signal';
        }

        return $affected;
    }

    private function buildRecommendations(string $biasLevel, array $ratios): array
    {
        $recommendations = [];

        $recommendations[] = 'All AI decisions should be reviewed by a human before final action.';
        $recommendations[] = 'Ensure the job description uses inclusive, gender-neutral language.';

        if (in_array($biasLevel, [AIBiasReport::LEVEL_HIGH, AIBiasReport::LEVEL_CRITICAL], true)) {
            $recommendations[] = 'URGENT: Pause automated shortlisting and conduct manual audit.';
            $recommendations[] = 'Engage a diversity & inclusion specialist to review hiring criteria.';
            $recommendations[] = 'Consider retraining or recalibrating the AI model with balanced data.';
        }

        if ($biasLevel === AIBiasReport::LEVEL_MODERATE) {
            $recommendations[] = 'Increase human review percentage for shortlisted and rejected candidates.';
            $recommendations[] = 'Blind screening (remove names/photos) can reduce implicit bias signals.';
        }

        if (($ratios['override_rate'] ?? 0) > 15) {
            $recommendations[] = 'High override rate detected — AI model calibration may be needed.';
        }

        return $recommendations;
    }

    private function percentile(array $values, int $pct): float
    {
        if (empty($values)) {
            return 0.0;
        }
        sort($values);
        $index = ($pct / 100) * (count($values) - 1);
        $lower = (int) floor($index);
        $upper = (int) ceil($index);
        if ($lower === $upper) {
            return (float) $values[$lower];
        }
        return round($values[$lower] + ($index - $lower) * ($values[$upper] - $values[$lower]), 2);
    }
}
