<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessBulkApplicationClose;
use App\Jobs\ScoreAndRankCandidates;
use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Runs on a cron schedule (hourly).
 * Checks job lifecycle dates and dispatches appropriate queue jobs.
 */
class ProcessJobApplicationDeadlines extends Command
{
    protected $signature   = 'orin:process-deadlines';
    protected $description = 'Process application close/evaluation dates and trigger Orin™ pipeline jobs';

    public function handle(): int
    {
        $today = now()->toDateString();

        // ── 1. Close applications where close_date has passed ──────────────────
        $jobsToClose = Job::where('application_phase', 'open')
            ->whereDate('close_date', '<=', $today)
            ->get();

        foreach ($jobsToClose as $job) {
            $this->info("Closing applications for job #{$job->id}: {$job->title}");
            ProcessBulkApplicationClose::dispatch($job->id)->onQueue('notifications');
            Log::info('Orin: Dispatched ProcessBulkApplicationClose', ['job_id' => $job->id]);
        }

        // ── 2. Begin evaluation phase when eval_start_date arrives ─────────────
        $jobsToEvaluate = Job::where('application_phase', 'closed')
            ->whereDate('eval_start_date', '<=', $today)
            ->get();

        foreach ($jobsToEvaluate as $job) {
            $this->info("Starting evaluation phase for job #{$job->id}: {$job->title}");
            $job->update(['application_phase' => 'evaluating']);
            Log::info('Orin: Job phase updated to evaluating', ['job_id' => $job->id]);
        }

        // ── 3. Score and rank when final_date arrives ───────────────────────────
        $jobsToRank = Job::where('application_phase', 'evaluating')
            ->whereDate('final_date', '<=', $today)
            ->get();

        foreach ($jobsToRank as $job) {
            $this->info("Ranking candidates for job #{$job->id}: {$job->title}");
            ScoreAndRankCandidates::dispatch($job->id)->onQueue('ai-processing');
            Log::info('Orin: Dispatched ScoreAndRankCandidates', ['job_id' => $job->id]);
        }

        $total = count($jobsToClose) + count($jobsToEvaluate) + count($jobsToRank);
        $this->info("Done. Processed {$total} job deadline(s).");

        return self::SUCCESS;
    }
}
