<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CandidateRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Application $application
    ) {
        $this->queue = 'notifications';
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $jobTitle = $this->application->job->title ?? 'a position';
        $company  = $this->application->job->company?->name ?? $this->application->job->company_name ?? 'a company';
        $reason   = $this->application->rejection_reason
            ? ' Reason: ' . $this->application->rejection_reason
            : '';

        return [
            'type'           => 'application_rejected',
            'application_id' => $this->application->id,
            'message'        => "Your application for {$jobTitle} at {$company} was not selected at this time.{$reason}",
            'url'            => url('/applications/' . $this->application->id),
        ];
    }
}
