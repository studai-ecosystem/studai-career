<?php

declare(strict_types=1);

namespace App\Services\ResponsibleAI;

use App\Models\AIDecisionLog;
use App\Models\HumanOverride;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * ExplainableAIService
 *
 * Records every AI decision with human-readable explanations (XAI).
 * Every AI score produced by this platform must be logged here.
 */
class ExplainableAIService
{
    /**
     * Record an AI decision with full XAI explanation.
     *
     * @param  string  $subjectType   Morph type: Application, User, Resume, etc.
     * @param  int     $subjectId
     * @param  string  $decisionType  AIDecisionLog::TYPE_* constant
     * @param  array   $payload       {
     *   'score'            => float,
     *   'recommendation'   => string,
     *   'confidence'       => float,
     *   'model'            => string,
     *   'factors'          => [{factor, weight, value, contribution, description}],
     *   'evidence'         => [{label, excerpt}],
     *   'explanation'      => string (natural language),
     *   'input_context'    => array,
     *   'raw_response'     => array,
     *   'processing_ms'    => int,
     * }
     */
    public function record(
        string $subjectType,
        int $subjectId,
        string $decisionType,
        array $payload
    ): AIDecisionLog {
        try {
            $biasResult = app(BiasDetectionService::class)->quickScan($payload);

            $log = AIDecisionLog::create([
                'subject_type'                => $subjectType,
                'subject_id'                  => $subjectId,
                'actor_id'                    => Auth::id(),
                'actor_type'                  => Auth::check() ? 'User' : 'System',
                'decision_type'               => $decisionType,
                'model_used'                  => $payload['model'] ?? null,
                'ai_score'                    => $payload['score'] ?? null,
                'ai_recommendation'           => $payload['recommendation'] ?? null,
                'confidence'                  => $payload['confidence'] ?? null,
                'score_factors'               => $payload['factors'] ?? [],
                'evidence'                    => $payload['evidence'] ?? [],
                'natural_language_explanation' => $payload['explanation'] ?? null,
                'bias_flagged'                => $biasResult['flagged'],
                'bias_indicators'             => $biasResult['indicators'],
                'input_context'               => $payload['input_context'] ?? null,
                'raw_ai_response'             => $payload['raw_response'] ?? null,
                'processing_ms'               => $payload['processing_ms'] ?? null,
                'final_decision'              => $payload['recommendation'] ?? null,
                'was_overridden'              => false,
            ]);

            return $log;
        } catch (\Throwable $e) {
            Log::error('ExplainableAIService::record failed', [
                'subject_type' => $subjectType,
                'subject_id'   => $subjectId,
                'error'        => $e->getMessage(),
            ]);
            // Return a minimal unsaved log so callers don't break
            return new AIDecisionLog([
                'subject_type'    => $subjectType,
                'subject_id'      => $subjectId,
                'decision_type'   => $decisionType,
                'ai_score'        => $payload['score'] ?? null,
                'ai_recommendation' => $payload['recommendation'] ?? null,
            ]);
        }
    }

    /**
     * Build XAI factor breakdown from a raw score array (AutomatedShortlistingService style).
     *
     * Converts {'round_1': score, 'round_2': score, ...} into factor objects.
     */
    public function buildFactorsFromRounds(array $roundScores, array $roundDetails = []): array
    {
        $weights = [
            'round_1' => ['label' => 'Basic Qualification',  'weight' => 0.20],
            'round_2' => ['label' => 'Skills Competency',    'weight' => 0.30],
            'round_3' => ['label' => 'Cultural / DNA Fit',   'weight' => 0.25],
            'round_4' => ['label' => 'Experience & Holistic','weight' => 0.25],
        ];

        $factors = [];
        foreach ($roundScores as $round => $score) {
            $meta   = $weights[$round] ?? ['label' => ucfirst($round), 'weight' => 0.25];
            $contribution = round($score * $meta['weight'], 2);
            $factors[] = [
                'factor'       => $meta['label'],
                'weight'       => $meta['weight'],
                'value'        => round($score, 1),
                'contribution' => $contribution,
                'description'  => $roundDetails[$round]['summary'] ?? null,
                'strengths'    => $roundDetails[$round]['strengths'] ?? [],
                'concerns'     => $roundDetails[$round]['concerns'] ?? [],
            ];
        }
        return $factors;
    }

    /**
     * Build XAI factors from a named scores array (SuccessPredictor style).
     */
    public function buildFactorsFromNamedScores(array $namedScores): array
    {
        $meta = [
            'cultural_fit'        => ['label' => 'Cultural Fit',       'weight' => 0.30],
            'skill_fit'           => ['label' => 'Skill Fit',          'weight' => 0.30],
            'work_style_fit'      => ['label' => 'Work Style Fit',     'weight' => 0.20],
            'performance_prediction' => ['label' => 'Performance Potential', 'weight' => 0.20],
            'resume'              => ['label' => 'Resume Quality',     'weight' => 0.35],
            'cover_letter'        => ['label' => 'Cover Letter',       'weight' => 0.20],
            'ats_alignment'       => ['label' => 'ATS Alignment',      'weight' => 0.30],
            'screening'           => ['label' => 'Screening Answers',  'weight' => 0.15],
        ];

        $factors = [];
        foreach ($namedScores as $key => $score) {
            if (! is_numeric($score)) {
                continue;
            }
            $m = $meta[$key] ?? ['label' => ucwords(str_replace('_', ' ', $key)), 'weight' => null];
            $factors[] = [
                'factor'       => $m['label'],
                'weight'       => $m['weight'],
                'value'        => round((float) $score, 1),
                'contribution' => $m['weight'] !== null ? round($score * $m['weight'], 2) : null,
            ];
        }
        return $factors;
    }

    /**
     * Generate a natural language explanation from score factors.
     */
    public function generateNaturalLanguageExplanation(
        float $score,
        string $recommendation,
        array $factors,
        string $candidateName = 'The candidate'
    ): string {
        $scoreLabel = match (true) {
            $score >= 85 => 'an excellent',
            $score >= 70 => 'a strong',
            $score >= 55 => 'a moderate',
            default      => 'a below-threshold',
        };

        $topFactors = collect($factors)
            ->sortByDesc('value')
            ->take(3)
            ->map(fn ($f) => sprintf('%s (%s%%)', $f['factor'], round($f['value'])))
            ->join(', ', ' and ');

        $weakFactors = collect($factors)
            ->filter(fn ($f) => ($f['value'] ?? 100) < 50)
            ->take(2)
            ->map(fn ($f) => $f['factor'])
            ->join(' and ');

        $recText = match ($recommendation) {
            'shortlist' => 'The AI recommends shortlisting this candidate.',
            'reject'    => 'The AI recommends not progressing this application.',
            'review'    => 'The AI recommends a manual review before deciding.',
            default     => 'The AI has flagged this for human review.',
        };

        $explanation = sprintf(
            '%s received %s overall AI match score of %d%%.',
            $candidateName,
            $scoreLabel,
            round($score)
        );

        if ($topFactors) {
            $explanation .= sprintf(' Strongest contributing factors: %s.', $topFactors);
        }

        if ($weakFactors) {
            $explanation .= sprintf(' Areas of concern: %s.', $weakFactors);
        }

        $explanation .= ' ' . $recText;
        $explanation .= ' This score is an AI-generated estimate and should be used as one input — human judgment and context always take precedence.';

        return $explanation;
    }

    /**
     * Get the most recent AI decision log for a given subject.
     */
    public function getLatestForSubject(string $subjectType, int $subjectId): ?AIDecisionLog
    {
        return AIDecisionLog::forSubject($subjectType, $subjectId)
            ->with('override')
            ->latest()
            ->first();
    }

    /**
     * Get all AI decision logs for a subject, newest first.
     */
    public function getHistoryForSubject(string $subjectType, int $subjectId): \Illuminate\Support\Collection
    {
        return AIDecisionLog::forSubject($subjectType, $subjectId)
            ->with(['override.overrider'])
            ->latest()
            ->get();
    }
}
