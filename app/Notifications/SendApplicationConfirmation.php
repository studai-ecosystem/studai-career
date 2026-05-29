<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendApplicationConfirmation extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $application;
    
    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }
    
    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }
    
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $job = $this->application->job;
        $company = $job->company;
        $hrEmail = $company?->hr_email ?? null;

        $applicationDate = $this->application->submitted_at
            ? $this->application->submitted_at->format('d M Y')
            : now()->format('d M Y');
        $closingDate = $job->close_date
            ? \Carbon\Carbon::parse($job->close_date)->format('d M Y')
            : 'To be announced';
        $evaluationDate = $job->eval_start_date
            ? \Carbon\Carbon::parse($job->eval_start_date)->format('d M Y')
            : 'To be announced';

        $message = (new MailMessage)
            ->subject('Application Submitted - ' . $job->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your application has been successfully submitted.')
            ->line('Position: ' . $job->title)
            ->line('Company: ' . $company->name)
            ->line('Application Number: ' . $this->application->application_number)
            ->line('Match Score: ' . $this->application->match_score . '%')
            ->line('Application Date: ' . $applicationDate)
            ->line('Applications Close: ' . $closingDate)
            ->line('Evaluation Begins: ' . $evaluationDate)
            ->action('View Application', url('/applications/' . $this->application->id))
            ->line('We will notify you when the employer reviews your application.')
            ->line('Good luck!');

        // Set Reply-To as company HR email so candidate replies go directly to HR
        if ($hrEmail) {
            $message->replyTo($hrEmail, $company->name . ' HR');
        }

        return $message;
    }
    
    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $jobTitle    = $this->application->job->title ?? 'a position';
        $companyName = $this->application->job->company->name ?? 'the employer';

        return [
            'application_id'     => $this->application->id,
            'application_number' => $this->application->application_number,
            'job_id'             => $this->application->job_id,
            'job_title'          => $jobTitle,
            'company_name'       => $companyName,
            'match_score'        => $this->application->match_score,
            'type'               => 'application_submitted',
            'message'            => "Application submitted for \"{$jobTitle}\" at {$companyName}",
            'url'                => url('/applications/' . $this->application->id),
        ];
    }
}
