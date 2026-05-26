<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendApplicationConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;
    public int $tries   = 3;

    public function __construct(public readonly int $applicationId)
    {
    }

    public function handle(): void
    {
        $application = Application::with('job.company')->find($this->applicationId);
        if (! $application) {
            return;
        }

        $job    = $application->job;
        $email  = $application->is_guest_applicant ? $application->guest_email : $application->user?->email;
        $name   = $application->is_guest_applicant ? $application->guest_name  : $application->user?->name;
        $appNum = 'APP-' . str_pad($application->id, 6, '0', STR_PAD_LEFT);
        $evalDate = $job->eval_start_date?->format('l, d F Y') ?? 'to be announced';
        $company = $job->company?->name ?? 'the company';

        if (! $email) {
            return;
        }

        try {
            Mail::send([], [], function ($m) use ($email, $name, $job, $appNum, $evalDate, $company) {
                $m->to($email, $name)
                  ->subject("Application received — {$job->title} at {$company}")
                  ->html(<<<HTML
<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;color:#333">
<h1 style="color:#1A73E8">StudAI Hire</h1>
<h2>Application Received ✅</h2>
<p>Hi {$name},</p>
<p>Your application for <strong>{$job->title}</strong> at <strong>{$company}</strong> has been received.</p>
<table style="background:#f8f9fa;padding:16px;border-radius:8px;width:100%;margin:20px 0">
  <tr><td><strong>Application Number:</strong></td><td>{$appNum}</td></tr>
  <tr><td><strong>AI Evaluation Date:</strong></td><td>{$evalDate}</td></tr>
</table>
<p>On the evaluation date, you will receive a link to begin your personalised Orin™ evaluation. Set aside 45–60 minutes in a quiet environment.</p>
<p>You can check your application status at any time using your original application link.</p>
<hr style="border:none;border-top:1px solid #eee;margin:30px 0">
<p style="color:#999;font-size:12px;text-align:center">StudAI Edutech Pvt. Ltd. | Powered by Orin™ AI Engine</p>
</body></html>
HTML);
            });

            $application->update(['application_email_sent' => true]);
        } catch (\Exception $e) {
            Log::error('SendApplicationConfirmationEmail: Failed', [
                'application_id' => $this->applicationId,
                'error'          => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
