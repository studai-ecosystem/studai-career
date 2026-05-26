<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyEmployer extends Notification implements ShouldQueue
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
        $applicant = $this->application->user;
        
        return (new MailMessage)
            ->subject('New Application for ' . $job->title)
            ->greeting('Hello!')
            ->line('You have received a new application for the position: ' . $job->title)
            ->line('Applicant: ' . $applicant->name)
            ->line('Match Score: ' . $this->application->match_score . '%')
            ->action('View Application', url('/employer/applications/' . $this->application->id))
            ->line('Thank you for using StudAI Hire Platform!');
    }
    
    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $job       = $this->application->job;
        $applicant = $this->application->user;

        return [
            'application_id' => $this->application->id,
            'job_id'         => $this->application->job_id,
            'applicant_name' => $applicant?->name ?? 'A candidate',
            'job_title'      => $job?->title ?? 'your job',
            'match_score'    => $this->application->match_score,
            // 'message' key is what the notification bell reads
            'message' => ($applicant?->name ?? 'A candidate') . ' applied for "' . ($job?->title ?? 'your job') . '"',
            'url'     => route('employer.applicants.show', $this->application->id),
            'type'    => 'new_application',
        ];
    }
}
