<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\MarketplaceProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalOfferNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly MarketplaceProposal $proposal) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project  = $this->proposal->project;
        $employer = $project?->employer;
        $amount   = '₹' . number_format((float) $this->proposal->proposed_amount);

        return (new MailMessage())
            ->subject('🎉 You\'ve been selected! Offer from ' . ($employer?->name ?? 'a company'))
            ->greeting('Congratulations ' . ($notifiable->name ?? '') . '!')
            ->line('**' . ($employer?->name ?? 'A company') . '** has reviewed your proposal and wants to hire you.')
            ->line('**Project:** ' . ($project?->title ?? ''))
            ->line('**Amount:** ' . $amount)
            ->line('**Delivery:** ' . ($this->proposal->estimated_duration_days ?? '—') . ' days')
            ->action('Accept or Decline Offer', route('marketplace.freelancer.offers'))
            ->line('This offer will expire in **7 days**. Please respond promptly.')
            ->salutation('StudAI Marketplace');
    }

    public function toArray(object $notifiable): array
    {
        $amount = '₹' . number_format((float) $this->proposal->proposed_amount);
        $title  = $this->proposal->project?->title ?? 'a project';

        return [
            'type'          => 'proposal_offer',
            'proposal_id'   => $this->proposal->id,
            'project_id'    => $this->proposal->project_id,
            'project_title' => $title,
            'amount'        => $this->proposal->proposed_amount,
            'message'       => "🎉 You received an offer for \"{$title}\" — {$amount}",
            'url'           => route('marketplace.freelancer.offers'),
        ];
    }
}
