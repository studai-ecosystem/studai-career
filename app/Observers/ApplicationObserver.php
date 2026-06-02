<?php

namespace App\Observers;

use App\Events\HireOutcomeRecorded;
use App\Models\Application;
use App\Models\HireOutcome;
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
                // C4: capture terminal outcomes (hired / rejected) with a
                // snapshot of the composite scores that informed ranking.
                if (in_array($application->status, ['hired', 'rejected'], true)) {
                    $this->recordHireOutcome($application);
                }

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

    /**
     * Persist a ground-truth HireOutcome record (idempotent per application)
     * and fire the decoupled HireOutcomeRecorded event. Failures here must
     * never break the status-change flow.
     */
    private function recordHireOutcome(Application $application): void
    {
        try {
            $companyId = $application->company_id
                ?? $application->job?->company_id
                ?? $application->job()->value('company_id');

            $outcome = HireOutcome::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'user_id'               => $application->user_id,
                    'job_id'                => $application->job_id,
                    'company_id'            => $companyId,
                    'outcome'               => $application->status,
                    'evaluation_score'      => $application->evaluation_score,
                    'skill_match_score'     => $application->skill_match_score,
                    'resume_quality_score'  => $application->resume_quality_score,
                    'behavioural_fit_score' => $application->behavioural_fit_score,
                    'final_rank_score'      => $application->final_rank_score,
                    'decided_at'            => now(),
                ]
            );

            HireOutcomeRecorded::dispatch($outcome);
        } catch (\Throwable $e) {
            \Log::warning('ApplicationObserver: failed to record hire outcome: ' . $e->getMessage());
        }
    }
}
