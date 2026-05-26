<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateHiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $emailSubject,
        public readonly string $body,
        public readonly string $candidateName,
        public readonly string $jobTitle,
        public readonly string $companyName,
        public readonly string $eventType,       // shortlisted | interviewed | hired | rejected
        public readonly float  $matchScore = 0.0,
        public readonly string $rejectionReason = '',
        public readonly string $hrEmail = '',    // Reply-To for candidate
        public readonly string $studentTip = '', // AI-generated actionable tip
    ) {}

    public function envelope(): Envelope
    {
        $replyTo = $this->hrEmail
            ? [new Address($this->hrEmail, $this->companyName . ' HR')]
            : [];

        return new Envelope(
            subject:  $this->emailSubject,
            replyTo:  $replyTo,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.candidate-hiring');
    }

    public function attachments(): array
    {
        return [];
    }
}
