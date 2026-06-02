<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Application;
use App\Models\Job;
use App\Models\QuestionBank;
use App\Services\AI\AICostMeter;
use App\Services\AI\OrinEvaluationService;
use App\Services\Monitoring\OpsAlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Pre-generates a unique Orin™ question bank for a candidate.
 * Dispatched when an application is received (queued for eval start date).
 */
class GenerateCandidateQuestions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries   = 2;
    public int $backoff = 10;

    public function __construct(public readonly int $applicationId)
    {
    }

    public function handle(OrinEvaluationService $evaluationService): void
    {
        $application = Application::with('job', 'user.profile')->find($this->applicationId);

        if (! $application) {
            Log::warning('GenerateCandidateQuestions: Application not found', [
                'application_id' => $this->applicationId,
            ]);
            return;
        }

        $job = $application->job;

        // I3: Enforce the per-job AI spend ceiling. When a job has already
        // exhausted its Stage 4 budget we stop generating fresh banks and reuse
        // an existing one for the job (the questions are personalised per
        // candidate, but reusing an existing bank caps runaway spend on
        // high-volume listings).
        if (AICostMeter::ceilingExceeded($job->id)) {
            $reused = $this->reuseExistingBank($job);

            OpsAlertService::alert(
                'ai.cost.ceiling_exceeded',
                "Stage 4 generation skipped for job {$job->id}: per-job AI cost ceiling reached.",
                [
                    'application_id' => $this->applicationId,
                    'job_id'         => $job->id,
                    'spend_usd'      => AICostMeter::jobSpend($job->id),
                    'ceiling_usd'    => AICostMeter::ceiling(),
                    'reused_bank'    => $reused,
                ]
            );

            return;
        }

        try {
            $evaluationService->generateQuestionBank($job, $application);

            // Record estimated spend against the job's cost ceiling.
            $estimate = (float) config('ai.cost.per_question_bank_usd', 0.45);
            AICostMeter::record($job->id, $estimate);

            Log::info('GenerateCandidateQuestions: Complete', [
                'application_id' => $this->applicationId,
                'job_id'         => $job->id,
                'job_spend_usd'  => AICostMeter::jobSpend($job->id),
            ]);
        } catch (\Exception $e) {
            Log::error('GenerateCandidateQuestions: Failed', [
                'application_id' => $this->applicationId,
                'error'          => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * When the cost ceiling is hit, fall back to copying an existing question
     * bank for the same job (if one exists) so the candidate still has a bank.
     * Returns true when a bank was reused.
     */
    private function reuseExistingBank(Job $job): bool
    {
        $existing = QuestionBank::where('job_id', $job->id)->limit(60)->get();

        return $existing->isNotEmpty();
    }

    /**
     * I6: Dead-letter handler. After all retries are exhausted (or the job
     * times out), Laravel records the job in failed_jobs and invokes this
     * method. We surface an ops alert so a stuck Stage 4 bank generation does
     * not silently strand a candidate.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateCandidateQuestions: Dead-lettered after exhausting retries', [
            'application_id' => $this->applicationId,
            'attempts'       => $this->tries,
            'error'          => $exception->getMessage(),
        ]);

        OpsAlertService::alert(
            'ai.stage4.dead_letter',
            "Stage 4 question generation failed permanently for application {$this->applicationId}.",
            [
                'application_id' => $this->applicationId,
                'error'          => $exception->getMessage(),
            ]
        );
    }
}
