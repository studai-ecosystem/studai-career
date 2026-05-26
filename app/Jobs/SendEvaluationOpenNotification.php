<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends evaluation open notification to a single applicant.
 * Dispatched in bulk by ProcessBulkApplicationClose.
 */
class SendEvaluationOpenNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;
    public int $tries   = 3;

    public function __construct(
        public readonly int $applicationId,
        public readonly int $jobId,
    ) {
    }

    public function handle(): void
    {
        $application = Application::with('job.company')->find($this->applicationId);
        if (! $application) {
            return;
        }

        $job = $application->job;
        $evalDate = $job->eval_start_date?->format('l, d F Y') ?? 'soon';
        $applyLink = config('app.url') . '/apply/' . $job->application_link_token;

        $recipientEmail = $application->is_guest_applicant
            ? $application->guest_email
            : $application->user?->email;

        $recipientName = $application->is_guest_applicant
            ? $application->guest_name
            : $application->user?->name;

        if (! $recipientEmail) {
            Log::warning('SendEvaluationOpenNotification: No email', ['application_id' => $this->applicationId]);
            return;
        }

        Mail::send([], [], function ($message) use ($recipientEmail, $recipientName, $job, $evalDate, $applyLink) {
            $message->to($recipientEmail, $recipientName)
                ->subject("Your evaluation for {$job->title} begins on {$evalDate}")
                ->html($this->buildEmailHtml($recipientName, $job, $evalDate, $applyLink));
        });

        $application->update(['evaluation_invite_sent' => true]);
    }

    private function buildEmailHtml(string $name, Job $job, string $evalDate, string $link): string
    {
        $companyName = $job->company?->name ?? 'the company';
        return <<<HTML
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; color: #333;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #1A73E8; font-size: 24px;">StudAI Hire</h1>
        <p style="color: #666; font-size: 14px;">Powered by Orin™</p>
    </div>
    <h2 style="color: #1A73E8;">Your evaluation is opening soon</h2>
    <p>Hi {$name},</p>
    <p>Applications for <strong>{$job->title}</strong> at <strong>{$companyName}</strong> are now closed.</p>
    <p>Your personalised AI evaluation begins on: <strong>{$evalDate}</strong></p>
    <p>When the evaluation opens, simply use your original application link to access it. Orin™ has prepared questions specifically tailored to your background and this role.</p>
    <div style="text-align: center; margin: 30px 0;">
        <a href="{$link}" style="background: #1A73E8; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
            Access Your Application
        </a>
    </div>
    <p style="color: #666; font-size: 13px;">Tip: Set aside 45-60 minutes for the evaluation in a quiet environment with a stable internet connection.</p>
    <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
    <p style="color: #999; font-size: 12px; text-align: center;">StudAI Edutech Pvt. Ltd. | CIN: U85500TN2024PTC168744</p>
</body>
</html>
HTML;
    }
}
