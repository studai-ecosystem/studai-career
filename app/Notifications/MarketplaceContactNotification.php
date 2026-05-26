<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\MarketplaceProject;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarketplaceContactNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $sender,
        private readonly MarketplaceProject $project,
        private readonly string $subject,
        private readonly string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('[StudAI] ' . $this->subject)
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('You have received a message from **' . $this->sender->name . '** regarding your project:')
            ->line('> **' . $this->project->title . '**')
            ->line('---')
            ->line($this->message)
            ->line('---')
            ->action('View Project', route('marketplace.project.show', $this->project))
            ->salutation('StudAI Marketplace');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'marketplace_contact',
            'sender_id'   => $this->sender->id,
            'sender_name' => $this->sender->name,
            'project_id'  => $this->project->id,
            'project_title' => $this->project->title,
            'subject'     => $this->subject,
            'message'     => $this->message,
        ];
    }
}
