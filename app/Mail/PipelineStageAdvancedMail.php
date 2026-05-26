<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PipelineStageAdvancedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $candidateName;
    public string $jobTitle;
    public string $companyName;
    public string $stage;
    public string $stageLabel;
    public string $scheduledDate;
    public string $notes;
    public string $testLink;

    /** Stage labels for email */
    private const STAGE_LABELS = [
        'company_info_test' => 'Company Info Test',
        'aptitude'          => 'Aptitude Assessment',
        'tech_test'         => 'Technical Test',
        'non_tech_test'     => 'Non-Technical Test',
    ];

    public function __construct(Application $application, string $stage, ?string $notes = null)
    {
        $job = $application->job;
        $this->candidateName  = $application->user->name ?? 'Candidate';
        $this->jobTitle       = $job->title ?? 'the position';
        $this->companyName    = $job->company->name ?? 'the company';
        $this->stage          = $stage;
        $this->stageLabel     = self::STAGE_LABELS[$stage] ?? ucwords(str_replace('_', ' ', $stage));
        $this->scheduledDate  = $application->pipeline_stage_date
            ? \Carbon\Carbon::parse($application->pipeline_stage_date)->format('d M Y')
            : 'To be confirmed';
        $this->notes          = $notes ?? '';

        // Generate test link token if not set
        if (!$application->test_link_token) {
            $application->updateQuietly(['test_link_token' => Str::random(64)]);
            $application->refresh();
        }

        $testStages = ['company_info_test', 'aptitude', 'tech_test', 'non_tech_test'];
        $this->testLink = in_array($stage, $testStages)
            ? url('/hiring-test/' . $application->test_link_token . '/' . $stage)
            : '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📋 Next Step: {$this->stageLabel} — {$this->jobTitle} at {$this->companyName}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pipeline-stage-advanced');
    }

    public function attachments(): array
    {
        return [];
    }
}
