<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Application $application,
        public readonly string      $hrName = 'HR Team',
    ) {}

    public function envelope(): Envelope
    {
        $jobTitle    = $this->application->job->title ?? 'a position';
        $candidate   = $this->application->user?->name ?? 'A candidate';

        return new Envelope(
            subject: "📩 New Application: {$candidate} — {$jobTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-application-hr');
    }

    public function attachments(): array
    {
        return [];
    }
}
