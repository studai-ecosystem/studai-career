<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Application;
use App\Models\BulkEmailLog;
use App\Models\Job;
use App\Services\Monitoring\OpsAlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Ranks all candidates for a job after evaluations close.
 * Computes composite scores with bias correction and updates rank_position.
 * Then triggers shortlist/rejection notifications and skill feedback emails.
 */
class ScoreAndRankCandidates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 2;

    public function __construct(public readonly int $jobId)
    {
    }

    public function handle(): void
    {
        $job = Job::find($this->jobId);
        if (! $job) {
            Log::warning('ScoreAndRankCandidates: Job not found', ['job_id' => $this->jobId]);
            return;
        }

        // F11: Idempotency — never re-rank a job that is already ranked/complete.
        if (in_array($job->application_phase, ['ranked', 'complete'], true)) {
            Log::info('ScoreAndRankCandidates: Already processed, skipping', [
                'job_id' => $job->id,
                'phase'  => $job->application_phase,
            ]);
            return;
        }

        // F11: Guard against concurrent double-dispatch with an atomic lock.
        $lock = Cache::lock("rank_job_{$job->id}", 300);
        if (! $lock->get()) {
            Log::info('ScoreAndRankCandidates: Lock held by another worker, skipping', [
                'job_id' => $job->id,
            ]);
            return;
        }

        try {
            $this->rank($job);
        } finally {
            $lock->release();
        }
    }

    private function rank(Job $job): void
    {
        // Re-check phase inside the lock (another worker may have just finished).
        $job->refresh();
        if (in_array($job->application_phase, ['ranked', 'complete'], true)) {
            Log::info('ScoreAndRankCandidates: Ranked while awaiting lock, skipping', [
                'job_id' => $job->id,
            ]);
            return;
        }

        // Get all completed evaluations
        $applications = Application::where('job_id', $job->id)
            ->where('evaluation_status', 'completed')
            ->with('evaluationSession')
            ->get();

        if ($applications->isEmpty()) {
            Log::info('ScoreAndRankCandidates: No completed evaluations', ['job_id' => $job->id]);
            return;
        }

        // F2: Validate composite ranking inputs. Halt + alert if any required
        // input is null — never silently default to 0 (would skew fairness).
        $requiredInputs = config('ai.ranking.required_inputs', [
            'evaluation_score',
            'skill_match_score',
            'resume_quality_score',
        ]);
        $missing = [];
        foreach ($applications as $app) {
            foreach ($requiredInputs as $field) {
                if ($app->{$field} === null) {
                    $missing[] = ['application_id' => $app->id, 'field' => $field];
                }
            }
        }

        if ($missing !== []) {
            $job->update(['application_phase' => 'ranking_blocked']);
            OpsAlertService::alert(
                'ranking.blocked_missing_inputs',
                "Ranking halted for job {$job->id}: required composite inputs are missing.",
                [
                    'job_id'        => $job->id,
                    'missing_count' => count($missing),
                    'missing'       => array_slice($missing, 0, 25),
                ]
            );
            return;
        }

        $weights = config('ai.ranking.weights', [
            'evaluation_score'      => 0.45,
            'skill_match_score'     => 0.25,
            'resume_quality_score'  => 0.15,
            'behavioural_fit_score' => 0.15,
        ]);
        $applyCheatPenalty = (bool) config('ai.ranking.apply_cheat_penalty', false);

        // Compute composite score for each application.
        $ranked = $applications->map(function (Application $app) use ($weights, $applyCheatPenalty) {
            $evalScore   = (float) ($app->evaluation_score ?? 0);
            $skillMatch  = (float) ($app->skill_match_score ?? 0);
            $resumeScore = (float) ($app->resume_quality_score ?? 0);
            $behavScore  = (float) ($app->behavioural_fit_score ?? 0);

            // F3: Anti-cheat is flag-for-human-review only. No automatic score
            // penalty unless explicitly enabled via config (default: off).
            $cheatPenalty = 0.0;
            $session = $app->evaluationSession;
            if ($applyCheatPenalty && $session) {
                $cheatPenalty = min(20.0, ($session->tab_switch_count ?? 0) * 2.0);
                if ($session->flagged_for_review) {
                    $cheatPenalty += 10.0;
                }
            }

            // Weighted composite score
            $composite = (
                ($evalScore   * $weights['evaluation_score']) +
                ($skillMatch  * $weights['skill_match_score']) +
                ($resumeScore * $weights['resume_quality_score']) +
                ($behavScore  * $weights['behavioural_fit_score'])
            ) - $cheatPenalty;

            return [
                'application'     => $app,
                'composite_score' => max(0.0, round($composite, 2)),
                'flagged'         => (bool) ($session->flagged_for_review ?? false),
            ];
        })->sortByDesc('composite_score')->values();

        // Assign rank positions and persist
        foreach ($ranked as $index => $item) {
            $item['application']->update([
                'final_rank_score' => $item['composite_score'],
                'rank_position'    => $index + 1,
            ]);
        }

        // F3: Surface flagged sessions to recruiters for human review.
        $flaggedIds = $ranked->where('flagged', true)
            ->map(fn ($item) => $item['application']->id)
            ->values()
            ->all();
        if ($flaggedIds !== []) {
            OpsAlertService::alert(
                'ranking.flagged_for_review',
                "Job {$job->id}: " . count($flaggedIds) . ' candidate(s) flagged for human review (anti-cheat).',
                ['job_id' => $job->id, 'application_ids' => $flaggedIds]
            );
        }

        // Update job phase
        $job->update(['application_phase' => 'ranked']);

        Log::info('ScoreAndRankCandidates: Ranked', [
            'job_id' => $job->id,
            'count'  => $applications->count(),
        ]);

        // Dispatch shortlist notifications
        SendShortlistNotifications::dispatch($job->id)->onQueue('notifications');

        // Dispatch personalised skill feedback emails to every evaluated candidate
        foreach ($ranked as $item) {
            SendSkillFeedbackEmail::dispatch($item['application']->id)->onQueue('notifications');
        }
    }
}
