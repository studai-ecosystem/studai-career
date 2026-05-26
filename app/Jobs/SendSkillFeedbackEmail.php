<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Application;
use App\Services\AI\OrinEvaluationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends Orin™ personalised skill feedback to a candidate after their evaluation
 * results are finalised and ranking is complete.
 */
class SendSkillFeedbackEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries   = 2;

    public function __construct(public readonly int $applicationId)
    {
    }

    public function handle(OrinEvaluationService $orinEvalService): void
    {
        $application = Application::with(['job.company', 'user', 'evaluationSession'])
            ->find($this->applicationId);

        if (! $application) {
            Log::warning('SendSkillFeedbackEmail: Application not found', ['id' => $this->applicationId]);
            return;
        }

        // Determine recipient email
        $email = $application->is_guest_applicant
            ? $application->guest_email
            : $application->user?->email;

        $name = $application->is_guest_applicant
            ? $application->guest_name
            : ($application->user?->name ?? 'Candidate');

        if (empty($email)) {
            Log::warning('SendSkillFeedbackEmail: No email for application', ['id' => $this->applicationId]);
            return;
        }

        $job        = $application->job;
        $company    = $job->company;
        $evalScore  = number_format((float) ($application->evaluation_score ?? 0), 1);
        $finalScore = number_format((float) ($application->final_rank_score ?? 0), 1);
        $rank       = $application->rank_position;

        // Generate personalised AI feedback
        $feedbackHtml = $orinEvalService->generateSkillFeedback($application);

        $subject = "Your Orin™ Evaluation Results — {$job->title}";

        $rankHtml = $rank
            ? "<div class=\"score-item\"><div class=\"score-value\">#{$rank}</div><div class=\"score-label\">Your Rank</div></div>"
            : '';

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$subject}</title>
<style>
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f8fafc;margin:0;padding:20px}
  .container{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.06)}
  .header{background:linear-gradient(135deg,#1A73E8,#0D47A1);padding:36px 32px;text-align:center}
  .header h1{color:#fff;margin:0;font-size:22px;font-weight:700}
  .header p{color:rgba(255,255,255,.8);margin:6px 0 0;font-size:14px}
  .badge{display:inline-block;background:rgba(255,255,255,.2);color:#fff;border-radius:100px;padding:4px 14px;font-size:12px;font-weight:600;margin-top:12px;border:1px solid rgba(255,255,255,.3)}
  .body{padding:32px}
  .greeting{font-size:16px;color:#1a1a1a;margin-bottom:8px}
  .score-card{background:#f0f7ff;border:1px solid #bfdbfe;border-radius:12px;padding:20px;margin:20px 0;display:flex;gap:16px;flex-wrap:wrap}
  .score-item{flex:1;min-width:100px;text-align:center}
  .score-value{font-size:32px;font-weight:800;color:#1A73E8;line-height:1}
  .score-label{font-size:11px;color:#64748b;margin-top:4px;text-transform:uppercase;letter-spacing:.5px}
  .feedback-section{margin-top:24px}
  .feedback-section h3{color:#1A73E8;font-size:15px;margin-bottom:6px}
  .feedback-section p,.feedback-section li{font-size:14px;color:#374151;line-height:1.6}
  .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 32px;text-align:center}
  .footer p{font-size:12px;color:#94a3b8;margin:4px 0}
  .orin-badge{font-weight:700;color:#1A73E8}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Your Evaluation Results are Ready</h1>
    <p>{$job->title} at {$company->name}</p>
    <span class="badge">Powered by Orin&#x2122; AI</span>
  </div>
  <div class="body">
    <p class="greeting">Hi {$name},</p>
    <p style="color:#374151;font-size:14px">Thank you for completing the <strong>Orin&#x2122; Adaptive Evaluation</strong>. Here is your personalised performance summary:</p>

    <div class="score-card">
      <div class="score-item">
        <div class="score-value">{$evalScore}</div>
        <div class="score-label">Evaluation Score</div>
      </div>
      <div class="score-item">
        <div class="score-value">{$finalScore}</div>
        <div class="score-label">Composite Score</div>
      </div>
      {$rankHtml}
    </div>

    <div class="feedback-section">
      {$feedbackHtml}
    </div>

    <p style="font-size:13px;color:#64748b;margin-top:24px">
      This is an automated performance report generated by <span class="orin-badge">Orin&#x2122;</span> AI.
      Your application is still under review &mdash; you will receive a separate communication regarding the hiring decision.
    </p>
  </div>
  <div class="footer">
    <p><strong>StudAI Hire</strong> &middot; Your Career, On Autopilot.</p>
    <p>This email was sent to {$email} as part of the application process.</p>
  </div>
</div>
</body>
</html>
HTML;

        try {
            Mail::send([], [], static function ($message) use ($email, $name, $subject, $htmlBody): void {
                $message->to($email, $name)
                    ->subject($subject)
                    ->html($htmlBody);
            });

            Log::info('SendSkillFeedbackEmail: Sent', ['application_id' => $this->applicationId, 'email' => $email]);
        } catch (\Exception $e) {
            Log::error('SendSkillFeedbackEmail: Failed', [
                'application_id' => $this->applicationId,
                'error'          => $e->getMessage(),
            ]);
            throw $e; // allow retry
        }
    }
}
