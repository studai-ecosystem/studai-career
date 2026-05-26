<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Application;
use App\Models\BulkEmailLog;
use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

        // Get all completed evaluations
        $applications = Application::where('job_id', $job->id)
            ->where('evaluation_status', 'completed')
            ->with('evaluationSession')
            ->get();

        if ($applications->isEmpty()) {
            Log::info('ScoreAndRankCandidates: No completed evaluations', ['job_id' => $job->id]);
            return;
        }

        // Compute composite score for each application (bias-corrected)
        $ranked = $applications->map(function (Application $app) {
            $evalScore    = (float) ($app->evaluation_score ?? 0);
            $skillMatch   = (float) ($app->skill_match_score ?? 0);
            $resumeScore  = (float) ($app->resume_quality_score ?? 0);
            $behavScore   = (float) ($app->behavioural_fit_score ?? 0);

            // Anti-cheat penalty
            $session = $app->evaluationSession;
            $cheatPenalty = 0.0;
            if ($session) {
                $cheatPenalty = min(20.0, ($session->tab_switch_count ?? 0) * 2.0);
                if ($session->flagged_for_review) {
                    $cheatPenalty += 10.0;
                }
            }

            // Weighted composite score
            $composite = (
                ($evalScore   * 0.45) +  // Evaluation performance (heaviest weight)
                ($skillMatch  * 0.25) +  // Skill match vs JD
                ($resumeScore * 0.15) +  // Resume quality
                ($behavScore  * 0.15)    // Culture fit
            ) - $cheatPenalty;

            return [
                'application'     => $app,
                'composite_score' => max(0.0, round($composite, 2)),
            ];
        })->sortByDesc('composite_score')->values();

        // Assign rank positions and persist
        foreach ($ranked as $index => $item) {
            $item['application']->update([
                'final_rank_score' => $item['composite_score'],
                'rank_position'    => $index + 1,
            ]);
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
