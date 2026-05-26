<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Application;
use App\Models\Job;
use App\Services\AI\OrinEvaluationService;
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

        try {
            $evaluationService->generateQuestionBank($job, $application);
            Log::info('GenerateCandidateQuestions: Complete', [
                'application_id' => $this->applicationId,
                'job_id'         => $job->id,
            ]);
        } catch (\Exception $e) {
            Log::error('GenerateCandidateQuestions: Failed', [
                'application_id' => $this->applicationId,
                'error'          => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
