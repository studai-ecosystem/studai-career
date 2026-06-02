<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Application;
use App\Models\BulkEmailLog;
use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends shortlisted / rejected emails to all candidates after ranking.
 * Also emails the employer with the ranked shortlist.
 */
class SendShortlistNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 2;

    public function __construct(public readonly int $jobId)
    {
    }

    public function handle(): void
    {
        $job = Job::with('company', 'poster')->find($this->jobId);
        if (! $job) {
            return;
        }

        $targetCount  = $job->target_hire_count ?? 1;
        $applications = Application::where('job_id', $job->id)
            ->whereNotNull('rank_position')
            ->orderBy('rank_position')
            ->get();

        $log = BulkEmailLog::create([
            'job_id'           => $job->id,
            'email_type'       => 'shortlisted',
            'total_recipients' => $applications->count() + 1, // +1 for employer
            'status'           => 'processing',
            'started_at'       => now(),
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($applications as $application) {
            $isShortlisted = $application->rank_position <= $targetCount;
            $email = $application->is_guest_applicant
                ? $application->guest_email
                : $application->user?->email;
            $name = $application->is_guest_applicant
                ? $application->guest_name
                : $application->user?->name;

            if (! $email) {
                continue;
            }

            try {
                if ($isShortlisted) {
                    Mail::send([], [], fn($m) => $m
                        ->to($email, $name)
                        ->subject("Congratulations! You've been shortlisted for {$job->title}")
                        ->html($this->buildShortlistEmail($name, $job, $application->rank_position))
                    );
                    $application->update([
                        'status'           => 'shortlisted',
                        'result_email_sent' => true,
                        'result_notified_at' => now(),
                    ]);
                } else {
                    Mail::send([], [], fn($m) => $m
                        ->to($email, $name)
                        ->subject("Update on your application for {$job->title}")
                        ->html($this->buildRejectionEmail($name, $job, $application))
                    );
                    $application->update([
                        'status'           => 'rejected',
                        'result_email_sent' => true,
                        'result_notified_at' => now(),
                    ]);
                }
                $sent++;
            } catch (\Exception $e) {
                Log::error('SendShortlistNotifications: Failed to send', [
                    'application_id' => $application->id,
                    'error'          => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        // Notify employer
        if ($job->poster?->email) {
            $shortlisted = $applications->where('rank_position', '<=', $targetCount);
            try {
                Mail::send([], [], fn($m) => $m
                    ->to($job->poster->email, $job->poster->name)
                    ->subject("Evaluation complete — Top {$targetCount} candidates for {$job->title}")
                    ->html($this->buildEmployerEmail($job, $shortlisted, $targetCount))
                );
                $sent++;
            } catch (\Exception $e) {
                Log::error('SendShortlistNotifications: Failed employer email', ['error' => $e->getMessage()]);
                $failed++;
            }
        }

        $job->update(['application_phase' => 'complete']);

        $log->update([
            'sent_count'   => $sent,
            'failed_count' => $failed,
            'status'       => 'complete',
            'completed_at' => now(),
        ]);
    }

    private function buildShortlistEmail(string $name, Job $job, int $rank): string
    {
        $company = $job->company?->name ?? 'the company';
        $applyLink = config('app.url') . '/apply/' . $job->application_link_token;
        return <<<HTML
<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;color:#333">
<h1 style="color:#1A73E8">StudAI Hire</h1>
<h2 style="color:#34A853">🎉 Congratulations, {$name}!</h2>
<p>You have been shortlisted for the position of <strong>{$job->title}</strong> at <strong>{$company}</strong>.</p>
<p>Our AI evaluation has ranked you in the <strong>top {$rank}</strong> candidates out of all applicants.</p>
<p>The hiring team will be in touch shortly with next steps.</p>
<div style="text-align:center;margin:30px 0">
  <a href="{$applyLink}" style="background:#1A73E8;color:white;padding:14px 28px;text-decoration:none;border-radius:6px;font-weight:bold">View Your Application Status</a>
</div>
<hr style="border:none;border-top:1px solid #eee;margin:30px 0">
<p style="color:#999;font-size:12px;text-align:center">StudAI Edutech Pvt. Ltd. | Powered by Orin™</p>
</body></html>
HTML;
    }

    private function buildRejectionEmail(string $name, Job $job, Application $application): string
    {
        $company = $job->company?->name ?? 'the company';
        $scoreBreakdown = $this->buildScoreBreakdown($application);
        return <<<HTML
<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;color:#333">
<h1 style="color:#1A73E8">StudAI Hire</h1>
<h2>Update on your application, {$name}</h2>
<p>Thank you for applying for <strong>{$job->title}</strong> at <strong>{$company}</strong> and for taking the time to complete our evaluation.</p>
<p>After careful review, we are unable to progress your application to the next stage at this time. This is not a reflection of your abilities — competition was strong.</p>
{$scoreBreakdown}
<p>Your performance report and personalised feedback from Orin™ will be available in your StudAI dashboard.</p>
<p>We encourage you to keep building your skills and apply for future opportunities.</p>
<hr style="border:none;border-top:1px solid #eee;margin:30px 0">
<p style="color:#999;font-size:12px;text-align:center">StudAI Edutech Pvt. Ltd. | Powered by Orin™</p>
</body></html>
HTML;
    }

    /**
     * F15: Build a transparent score breakdown (S.C.O.U.T. rounds + composite)
     * for inclusion in the rejection email. Pulls per-round scores from the
     * candidate's most recent AIDecisionLog when available, and always shows
     * the Orin™ component scores plus the final composite rank score.
     */
    private function buildScoreBreakdown(Application $application): string
    {
        $rows = [];

        $roundRows = $this->extractRoundScores($application);
        foreach ($roundRows as $label => $value) {
            $rows[] = "<tr><td style='padding:6px 8px;border-bottom:1px solid #eee'>{$label}</td>"
                . "<td style='padding:6px 8px;border-bottom:1px solid #eee;text-align:right'>{$value}</td></tr>";
        }

        $components = [
            'Evaluation score'    => $application->evaluation_score,
            'Skill match score'   => $application->skill_match_score,
            'Resume quality score' => $application->resume_quality_score,
        ];
        foreach ($components as $label => $value) {
            if ($value === null) {
                continue;
            }
            $formatted = number_format((float) $value, 1) . '/100';
            $rows[] = "<tr><td style='padding:6px 8px;border-bottom:1px solid #eee'>{$label}</td>"
                . "<td style='padding:6px 8px;border-bottom:1px solid #eee;text-align:right'>{$formatted}</td></tr>";
        }

        if ($application->final_rank_score !== null) {
            $composite = number_format((float) $application->final_rank_score, 1) . '/100';
            $rows[] = "<tr><td style='padding:8px;font-weight:bold'>Composite rank score</td>"
                . "<td style='padding:8px;text-align:right;font-weight:bold;color:#1A73E8'>{$composite}</td></tr>";
        }

        if ($rows === []) {
            return '';
        }

        $rowsHtml = implode('', $rows);

        return <<<HTML
<div style="margin:24px 0">
  <h3 style="margin:0 0 8px;font-size:15px;color:#333">Your evaluation results</h3>
  <table style="width:100%;border-collapse:collapse;font-size:14px;border:1px solid #eee">
    <tbody>{$rowsHtml}</tbody>
  </table>
</div>
HTML;
    }

    /**
     * Extract Round 1–4 S.C.O.U.T. scores from the candidate's latest decision log.
     *
     * @return array<string, string>
     */
    private function extractRoundScores(Application $application): array
    {
        $log = \App\Models\AIDecisionLog::query()
            ->where('subject_type', 'App\\Models\\Application')
            ->where('subject_id', $application->id)
            ->whereNotNull('score_factors')
            ->latest('id')
            ->first();

        if ($log === null || empty($log->score_factors)) {
            return [];
        }

        $rounds = [];
        foreach ($log->score_factors as $factor) {
            $name = $factor['factor'] ?? $factor['name'] ?? null;
            $value = $factor['value'] ?? $factor['contribution'] ?? null;
            if ($name === null || $value === null) {
                continue;
            }
            if (! preg_match('/round\s*[1-4]/i', (string) $name)) {
                continue;
            }
            $numeric = (float) $value;
            // Factors may be stored as 0–1 fractions or 0–100 scores.
            $display = $numeric <= 1 ? number_format($numeric * 100, 1) : number_format($numeric, 1);
            $rounds[(string) $name] = $display . '/100';
        }

        return $rounds;
    }

    private function buildEmployerEmail(Job $job, $shortlisted, int $targetCount): string
    {
        $company = $job->company?->name ?? 'your company';
        $dashboardLink = config('app.url') . '/employer/jobs/' . $job->id;
        $rows = $shortlisted->map(fn($a) => "
            <tr>
                <td style='padding:8px;border-bottom:1px solid #eee'>#{$a->rank_position}</td>
                <td style='padding:8px;border-bottom:1px solid #eee'>{$a->guest_name}</td>
                <td style='padding:8px;border-bottom:1px solid #eee'>{$a->final_rank_score}%</td>
            </tr>")->implode('');

        return <<<HTML
<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;color:#333">
<h1 style="color:#1A73E8">StudAI Hire — S.C.O.U.T</h1>
<h2>Evaluation complete: {$job->title}</h2>
<p>Orin™ has completed evaluation and ranking for all applicants. Your top {$targetCount} candidates are ready.</p>
<table style="width:100%;border-collapse:collapse;margin:20px 0">
  <thead><tr style="background:#f8f9fa">
    <th style="padding:8px;text-align:left">Rank</th>
    <th style="padding:8px;text-align:left">Candidate</th>
    <th style="padding:8px;text-align:left">Score</th>
  </tr></thead>
  <tbody>{$rows}</tbody>
</table>
<div style="text-align:center;margin:30px 0">
  <a href="{$dashboardLink}" style="background:#1A73E8;color:white;padding:14px 28px;text-decoration:none;border-radius:6px;font-weight:bold">View Full Rankings on Dashboard</a>
</div>
<hr style="border:none;border-top:1px solid #eee;margin:30px 0">
<p style="color:#999;font-size:12px;text-align:center">StudAI Edutech Pvt. Ltd. | CIN: U85500TN2024PTC168744</p>
</body></html>
HTML;
    }
}
