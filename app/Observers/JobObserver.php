<?php

namespace App\Observers;

use App\Models\Job;
use App\Jobs\Agent\ScanInternalJobsJob;
use App\Services\WebhookService;

class JobObserver
{
    protected WebhookService $webhookService;
    
    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the Job "created" event — trigger agent scan for new published jobs.
     */
    public function created(Job $job): void
    {
        if ($job->status === 'published') {
            ScanInternalJobsJob::dispatch();
        }
    }
    
    /**
     * Handle the Job "updated" event.
     */
    public function updated(Job $job): void
    {
        // When a job is newly published, trigger agent scan immediately
        if ($job->isDirty('status') && $job->status === 'published' && $job->getOriginal('status') !== 'published') {
            ScanInternalJobsJob::dispatch();

            $this->webhookService->trigger(
                'job.published',
                [
                    'job_id' => $job->id,
                    'title' => $job->title,
                    'category' => $job->category,
                    'location' => $job->location,
                    'employment_type' => $job->employment_type,
                    'work_mode' => $job->work_mode,
                    'published_at' => $job->published_at->toIso8601String(),
                    'expires_at' => $job->expires_at?->toIso8601String(),
                ],
                $job->company_id
            );
        }
        
        if ($job->isDirty('status') && $job->status === 'closed' && $job->getOriginal('status') !== 'closed') {
            $this->webhookService->trigger(
                'job.closed',
                [
                    'job_id' => $job->id,
                    'title' => $job->title,
                    'closed_at' => now()->toIso8601String(),
                    'total_applications' => $job->applications()->count(),
                ],
                $job->company_id
            );
        }
    }
}
