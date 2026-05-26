<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PipelineStageAdvancedNotification extends Notification
{
    use Queueable;

    private const STAGE_LABELS = [
        'company_info_test' => 'Company Info Test',
        'aptitude'          => 'Aptitude Assessment',
        'tech_test'         => 'Technical Test',
        'non_tech_test'     => 'Non-Technical Test',
        'hired'             => 'Hired 🎉',
        'rejected'          => 'Application Update',
    ];

    private const STAGE_ICONS = [
        'company_info_test' => '📋',
        'aptitude'          => '🧠',
        'tech_test'         => '💻',
        'non_tech_test'     => '📝',
        'hired'             => '🎉',
        'rejected'          => '📩',
    ];

    public function __construct(
        private readonly Application $application,
        private readonly string $stage,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $label     = self::STAGE_LABELS[$this->stage] ?? ucwords(str_replace('_', ' ', $this->stage));
        $icon      = self::STAGE_ICONS[$this->stage] ?? '📌';
        $jobTitle  = $this->application->job->title ?? 'a position';
        $company   = $this->application->job->company->name ?? 'the company';
        $token     = $this->application->test_link_token;

        $testStages = ['company_info_test', 'aptitude', 'tech_test', 'non_tech_test'];
        $url = $token && in_array($this->stage, $testStages)
            ? url('/hiring-test/' . $token . '/' . $this->stage)
            : url('/dashboard');

        $message = match ($this->stage) {
            'hired'    => "{$icon} Congratulations! You've been hired for {$jobTitle} at {$company}.",
            'rejected' => "Your application for {$jobTitle} at {$company} has been updated.",
            default    => "{$icon} You've been invited to the {$label} for {$jobTitle} at {$company}. Click to start your test.",
        };

        return [
            'title'   => "{$label} — {$jobTitle}",
            'message' => $message,
            'url'     => $url,
            'stage'   => $this->stage,
            'job'     => $jobTitle,
            'company' => $company,
        ];
    }
}
