<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\HireOutcome;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * C4: Offline S.C.O.U.T. threshold calibration consumer.
 *
 * Reads the ground-truth hire_outcomes table and reports *suggested* gate
 * thresholds derived from the score distributions of hired vs rejected
 * candidates. For operational safety this command NEVER mutates the live
 * thresholds in config/ai.php — a human reviews the report and updates the
 * SCOUT_THRESHOLD_* environment variables deliberately.
 */
class CalibrateScoutThresholds extends Command
{
    protected $signature = 'scout:calibrate-thresholds
                            {--min-samples=30 : Minimum hired+rejected samples required to report}
                            {--percentile=10 : Hired-score percentile used as the suggested threshold floor}';

    protected $description = 'Report suggested S.C.O.U.T. thresholds from hire outcomes (read-only, no live changes)';

    /**
     * Map each gated round to the HireOutcome score column and its config key.
     *
     * @var array<string, array{column: string, config: string, env: string, current: int}>
     */
    private array $gates;

    public function handle(): int
    {
        $this->gates = [
            'basic_qualification' => [
                'column' => 'evaluation_score',
                'config' => 'ai.scout.thresholds.basic_qualification',
                'env' => 'SCOUT_THRESHOLD_R1',
                'current' => (int) config('ai.scout.thresholds.basic_qualification', 60),
            ],
            'skills_competency' => [
                'column' => 'skill_match_score',
                'config' => 'ai.scout.thresholds.skills_competency',
                'env' => 'SCOUT_THRESHOLD_R2',
                'current' => (int) config('ai.scout.thresholds.skills_competency', 50),
            ],
            'cultural_fit' => [
                'column' => 'behavioural_fit_score',
                'config' => 'ai.scout.thresholds.cultural_fit',
                'env' => 'SCOUT_THRESHOLD_R3',
                'current' => (int) config('ai.scout.thresholds.cultural_fit', 60),
            ],
            'potential_growth' => [
                'column' => 'final_rank_score',
                'config' => 'ai.scout.thresholds.potential_growth',
                'env' => 'SCOUT_THRESHOLD_R4',
                'current' => (int) config('ai.scout.thresholds.potential_growth', 45),
            ],
        ];

        $minSamples = (int) $this->option('min-samples');
        $percentile = max(1, min(50, (int) $this->option('percentile')));

        $hired = HireOutcome::where('outcome', 'hired')->get();
        $rejected = HireOutcome::where('outcome', 'rejected')->get();

        $total = $hired->count() + $rejected->count();

        $this->info('S.C.O.U.T. Threshold Calibration (read-only)');
        $this->line("Hired samples: {$hired->count()} | Rejected samples: {$rejected->count()} | Total: {$total}");

        if ($total < $minSamples) {
            $this->warn("Not enough samples to calibrate (need at least {$minSamples}). No suggestions produced.");

            return self::SUCCESS;
        }

        $rows = [];
        $suggestions = [];

        foreach ($this->gates as $gate => $meta) {
            $hiredScores = $this->scores($hired, $meta['column']);
            $rejectedScores = $this->scores($rejected, $meta['column']);

            if ($hiredScores->isEmpty()) {
                $rows[] = [$gate, $meta['current'], '—', 'no hired scores'];

                continue;
            }

            $suggested = $this->percentile($hiredScores, $percentile);
            $hiredMean = round($hiredScores->avg(), 1);
            $rejectedMean = $rejectedScores->isEmpty() ? null : round($rejectedScores->avg(), 1);

            $note = $rejectedMean === null
                ? "hired μ={$hiredMean}"
                : "hired μ={$hiredMean}, rejected μ={$rejectedMean}";

            $rows[] = [$gate, $meta['current'], $suggested, $note];
            $suggestions[$meta['env']] = $suggested;
        }

        $this->table(
            ['Gate', 'Current', "Suggested (P{$percentile} hired)", 'Notes'],
            $rows
        );

        $this->newLine();
        $this->line('Suggested env overrides (apply manually after review):');
        foreach ($suggestions as $env => $value) {
            $this->line("  {$env}={$value}");
        }

        $this->newLine();
        $this->comment('No live thresholds were changed. Update .env deliberately if these suggestions are sound.');

        Log::info('Scout threshold calibration report generated', [
            'total_samples' => $total,
            'percentile' => $percentile,
            'suggestions' => $suggestions,
        ]);

        return self::SUCCESS;
    }

    /**
     * Extract non-null numeric scores for a column.
     */
    private function scores(Collection $outcomes, string $column): Collection
    {
        return $outcomes
            ->pluck($column)
            ->filter(fn ($v) => $v !== null)
            ->map(fn ($v) => (float) $v)
            ->values();
    }

    /**
     * Compute the given percentile (1-100) of a numeric collection.
     */
    private function percentile(Collection $scores, int $percentile): int
    {
        $sorted = $scores->sort()->values();
        $count = $sorted->count();

        if ($count === 0) {
            return 0;
        }

        $rank = (int) floor(($percentile / 100) * ($count - 1));

        return (int) round((float) $sorted[$rank]);
    }
}
