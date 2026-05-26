<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidateHiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Application $application
    ) {
        $this->queue = 'notifications';
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jobTitle = $this->application->job->title ?? 'a position';
        $company  = $this->application->job->company?->name ?? $this->application->job->company_name ?? 'a company';

        return (new MailMessage())
            ->subject("🎉 Congratulations — You've Been Hired!")
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("We are thrilled to inform you that you have been selected for **{$jobTitle}** at **{$company}**.")
            ->line("The employer will be in touch with you soon regarding the next steps.")
            ->action('View Application', url('/applications/' . $this->application->id))
            ->line('Best of luck in your new role!');
    }

    public function toArray(object $notifiable): array
    {
        $jobTitle = $this->application->job->title ?? 'a position';
        $company  = $this->application->job->company?->name ?? $this->application->job->company_name ?? 'a company';

        return [
            'type'           => 'candidate_hired',
            'application_id' => $this->application->id,
            'message'        => "🎉 Congratulations! You've been hired for {$jobTitle} at {$company}.",
            'url'            => url('/applications/' . $this->application->id),
        ];
    }
}
