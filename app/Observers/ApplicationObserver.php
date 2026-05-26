<?php

namespace App\Observers;

use App\Models\Application;
use App\Services\CacheService;
use App\Services\WebhookService;

class ApplicationObserver
{
    protected WebhookService $webhookService;
    
    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /** Bust caches on both sides whenever an application changes */
    private function bustCaches(Application $application): void
    {
        $companyId = $application->job?->company_id ?? $application->job()->value('company_id');
        if ($companyId) {
            CacheService::onApplicationChanged((int) $application->user_id, (int) $companyId);
        } else {
            CacheService::bustStudentCaches((int) $application->user_id);
        }
    }
    
    /**
     * Handle the Application "created" event.
     */
    public function created(Application $application): void
    {
        $this->bustCaches($application);

        try {
            $this->webhookService->trigger(
                'application.received',
                [
                    'application_id' => $application->id,
                    'job_id'         => $application->job_id,
                    'job_title'      => $application->job?->title ?? '',
                    'candidate_id'   => $application->user_id,
                    'candidate_name' => $application->user?->name ?? '',
                    'candidate_email'=> $application->user?->email ?? '',
                    'applied_at'     => ($application->submitted_at ?? now())->toIso8601String(),
                    'status'         => $application->status,
                    'source'         => $application->source,
                ],
                $application->job?->company_id
            );
        } catch (\Exception $e) {
            \Log::warning('ApplicationObserver webhook failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle the Application "updated" event.
     */
    public function updated(Application $application): void
    {
        $this->bustCaches($application);

        try {
            if ($application->isDirty('status')) {
                $this->webhookService->trigger(
                    'application.status_changed',
                    [
                        'application_id' => $application->id,
                        'job_id'         => $application->job_id,
                        'job_title'      => $application->job?->title ?? '',
                        'candidate_id'   => $application->user_id,
                        'candidate_name' => $application->user?->name ?? '',
                        'old_status'     => $application->getOriginal('status'),
                        'new_status'     => $application->status,
                        'updated_at'     => now()->toIso8601String(),
                    ],
                    $application->job?->company_id
                );

                if ($application->status === 'hired') {
                    $this->webhookService->trigger(
                        'candidate.hired',
                        [
                            'application_id' => $application->id,
                            'job_id'         => $application->job_id,
                            'job_title'      => $application->job?->title ?? '',
                            'candidate_id'   => $application->user_id,
                            'candidate_name' => $application->user?->name ?? '',
                            'candidate_email'=> $application->user?->email ?? '',
                            'hired_at'       => now()->toIso8601String(),
                        ],
                        $application->job?->company_id
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::warning('ApplicationObserver update webhook failed: ' . $e->getMessage());
        }
    }
}
