<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\MarketplaceProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly MarketplaceProposal $proposal,
        private readonly string $statusType  // 'shortlisted' | 'rejected'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->proposal->project;

        if ($this->statusType === 'shortlisted') {
            return (new MailMessage())
                ->subject('✅ Your proposal has been shortlisted!')
                ->greeting('Great news, ' . ($notifiable->name ?? '') . '!')
                ->line('Your proposal for **' . ($project?->title ?? '') . '** has been shortlisted by the hiring team.')
                ->line('They are reviewing it closely. You may receive an offer soon.')
                ->action('View Your Proposals', route('marketplace.freelancer.proposals'))
                ->salutation('StudAI Marketplace');
        }

        return (new MailMessage())
            ->subject('Proposal update for: ' . ($project?->title ?? ''))
            ->greeting('Hi ' . ($notifiable->name ?? '') . ',')
            ->line('Thank you for applying to **' . ($project?->title ?? '') . '**.')
            ->line('After careful review, the client has decided to move forward with another candidate. Your skills may be a better fit for other projects on our platform.')
            ->action('Browse More Projects', route('marketplace.projects'))
            ->salutation('StudAI Marketplace');
    }

    public function toArray(object $notifiable): array
    {
        $projectTitle = $this->proposal->project?->title ?? 'a project';
        $statusLabel  = $this->statusType === 'shortlisted' ? 'shortlisted ✅' : 'not selected';

        return [
            'type'          => 'proposal_' . $this->statusType,
            'proposal_id'   => $this->proposal->id,
            'project_id'    => $this->proposal->project_id,
            'project_title' => $projectTitle,
            'message'       => "Your proposal for \"{$projectTitle}\" has been {$statusLabel}",
            'url'           => route('marketplace.freelancer.proposals'),
        ];
    }
}
