<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HRHiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $emailSubject,
        public readonly string $body,
        public readonly string $candidateName,
        public readonly string $candidateEmail,
        public readonly string $jobTitle,
        public readonly string $companyName,
        public readonly string $eventType,
        public readonly float  $matchScore = 0.0,
        public readonly array  $profile = [],
        public readonly string $coverLetter = '',
        public readonly string $applicationNumber = '',
        public readonly string $appliedAt = '',
        public readonly string $rejectionReason = '',
        public readonly string $linkedinUrl = '',
        public readonly string $githubUrl = '',
        public readonly string $portfolioUrl = '',
        public readonly string $resumeUrl = '',
    ) {}

    public function envelope(): Envelope
    {
        // Reply-To is the candidate so HR can reply directly to them
        $replyTo = $this->candidateEmail && $this->candidateEmail !== 'N/A'
            ? [new Address($this->candidateEmail, $this->candidateName)]
            : [];

        return new Envelope(
            subject: '[HR] ' . $this->emailSubject,
            replyTo: $replyTo,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.hr-hiring');
    }

    public function attachments(): array
    {
        return [];
    }
}
