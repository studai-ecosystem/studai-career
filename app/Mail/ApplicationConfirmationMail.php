<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $candidateName;
    public string $jobTitle;
    public string $companyName;
    public string $applicationNumber;
    public string $applicationDate;
    public string $closingDate;
    public string $evaluationDate;

    public function __construct(Application $application)
    {
        $job = $application->job;
        $this->candidateName     = $application->user->name ?? 'Candidate';
        $this->jobTitle          = $job->title ?? 'the position';
        $this->companyName       = $job->company->name ?? 'the company';
        $this->applicationNumber = $application->application_number ?? '#' . $application->id;
        $this->applicationDate   = $application->submitted_at
            ? $application->submitted_at->format('d M Y')
            : now()->format('d M Y');
        $this->closingDate       = $job->close_date
            ? \Carbon\Carbon::parse($job->close_date)->format('d M Y')
            : 'To be announced';
        $this->evaluationDate    = $job->eval_start_date
            ? \Carbon\Carbon::parse($job->eval_start_date)->format('d M Y')
            : 'To be announced';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✅ Application Received — {$this->jobTitle} at {$this->companyName}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application-confirmation');
    }

    public function attachments(): array
    {
        return [];
    }
}
