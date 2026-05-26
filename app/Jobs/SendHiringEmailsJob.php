<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\CandidateHiringMail;
use App\Mail\HRHiringMail;
use App\Models\Application;
use App\Services\ApplicationReasonService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendHiringEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(
        protected Application $application,
        protected string $eventType, // shortlisted | interviewed | hired | rejected
        protected float $matchScore = 0.0,
    ) {}

    public function handle(ApplicationReasonService $reasonService): void
    {
        // Eager-load everything we need
        $this->application->loadMissing(['user.profile', 'job.company.owner', 'job.poster']);

        $jobTitle       = $this->application->job->title ?? 'a position';
        $companyName    = $this->application->job->company->name ?? ($this->application->job->company_name ?? 'the company');
        $candidateName  = $this->application->user->name ?? 'Candidate';
        $candidateEmail = $this->application->user->email ?? null;
        $hrEmail        = $this->application->job->company?->hr_email
                            ?? $this->application->job->company?->company_email
                            ?? $this->application->job->company?->owner?->email
                            ?? $this->application->job->poster?->email
                            ?? null;

        // ── 1. Generate AI reason (one call feeds all emails) ──────────────────
        $reason = $reasonService->generate($this->application, $this->eventType);

        // Persist the AI reason for audit trail
        $history   = $this->application->status_history ?? [];
        $history[] = [
            'status'          => $this->eventType,
            'changed_at'      => now()->toIso8601String(),
            'ai_reason'       => $reason['student_reason'],
            'company_summary' => $reason['company_summary'],
        ];

        $this->application->updateQuietly([
            'ai_reason'      => $reason['student_reason'],
            'status_history' => $history,
        ]);

        // Subject line templates
        $subjectMap = [
            'shortlisted' => "⭐ You've been shortlisted — {$jobTitle} at {$companyName}",
            'interviewed' => "📅 You've been selected to interview — {$jobTitle} at {$companyName}",
            'hired'       => "🎉 Congratulations — You're hired! {$jobTitle} at {$companyName}",
            'rejected'    => "Update on your application — {$jobTitle} at {$companyName}",
        ];
        $candidateSubject = $subjectMap[$this->eventType]
            ?? "Application update — {$jobTitle} at {$companyName}";

        // ── 2. Candidate email (all events) ───────────────────────────────────
        if ($candidateEmail) {
            try {
                Mail::to($candidateEmail)->send(new CandidateHiringMail(
                    emailSubject:    $candidateSubject,
                    body:            $reason['student_reason'],
                    candidateName:   $candidateName,
                    jobTitle:        $jobTitle,
                    companyName:     $companyName,
                    eventType:       $this->eventType,
                    matchScore:      $this->matchScore,
                    rejectionReason: $this->application->rejection_reason ?? '',
                    hrEmail:         $this->application->job->company?->hr_email ?? '',
                    studentTip:      $reason['student_tip'] ?? '',
                ));

                Log::info('HiringEmail: candidate email sent', [
                    'event' => $this->eventType,
                    'to'    => $candidateEmail,
                ]);
            } catch (\Throwable $e) {
                Log::error('HiringEmail: failed to send candidate email', [
                    'error'       => $e->getMessage(),
                    'application' => $this->application->id,
                ]);
            }
        }

        // Reset SMTP connection before second send — Gmail drops the socket after first mail
        try { app('mail.manager')->purge('smtp'); } catch (\Throwable $e) {}

        // ── 3. HR email (all events) ───────────────────────────────────────────
        if ($hrEmail) {
            try {
                $hrSubjectMap = [
                    'shortlisted' => "⭐ Shortlisted: {$candidateName} — {$jobTitle}",
                    'interviewed' => "📅 Interview Stage: {$candidateName} — {$jobTitle}",
                    'hired'       => "✅ Hired: {$candidateName} — {$jobTitle}",
                    'rejected'    => "✕ Rejected: {$candidateName} — {$jobTitle}",
                ];
                $hrSubject = $hrSubjectMap[$this->eventType]
                    ?? "Status update: {$candidateName} — {$jobTitle}";

                $profile = $this->application->user->profile;

                // Skills with proficiency
                $skillsFormatted = '';
                if (is_array($profile?->skills)) {
                    $pillParts = [];
                    foreach ($profile->skills as $skill) {
                        if (is_array($skill)) {
                            $n = $skill['name'] ?? $skill['skill'] ?? '';
                            $p = $skill['proficiency'] ?? $skill['level'] ?? '';
                            $pillParts[] = $n . ($p ? " ({$p})" : '');
                        } elseif (is_string($skill)) {
                            $pillParts[] = $skill;
                        }
                    }
                    $skillsFormatted = implode(', ', array_filter($pillParts));
                } elseif (is_string($profile?->skills)) {
                    $skillsFormatted = $profile->skills;
                }

                // Experience list
                $experienceList = '';
                if (is_array($profile?->experience)) {
                    foreach ($profile->experience as $exp) {
                        $t = $exp['title'] ?? $exp['position'] ?? '';
                        $c = $exp['company'] ?? '';
                        $d = trim(($exp['start_date'] ?? '') . ' – ' . ($exp['end_date'] ?? 'Present'));
                        if ($t || $c) {
                            $experienceList .= "{$t}" . ($c ? " at {$c}" : '') . " ({$d})\n";
                        }
                    }
                }

                // Education list
                $educationList = '';
                if (is_array($profile?->education)) {
                    foreach ($profile->education as $edu) {
                        $deg = $edu['degree'] ?? $edu['qualification'] ?? '';
                        $sch = $edu['institution'] ?? $edu['school'] ?? '';
                        $yr  = $edu['end_year'] ?? $edu['year'] ?? '';
                        if ($deg || $sch) {
                            $educationList .= $deg . ($sch ? " — {$sch}" : '') . ($yr ? " ({$yr})" : '') . "\n";
                        }
                    }
                }

                $profileData = [
                    'headline'             => $profile?->headline ?? '',
                    'summary'              => $profile?->summary ?? '',
                    'skills'               => $skillsFormatted,
                    'experience'           => $experienceList,
                    'education'            => $educationList,
                    'location'             => $profile?->current_location ?? '',
                    'work_preference'      => $profile?->work_preference ?? '',
                    'notice_period'        => $profile?->notice_period ?? '',
                    'expected_salary'      => $profile?->expected_salary_min && $profile?->expected_salary_max
                                                ? '₹' . number_format((float)$profile->expected_salary_min) . ' – ₹' . number_format((float)$profile->expected_salary_max)
                                                : '',
                    'profile_completeness' => $profile?->profile_completeness ?? 0,
                ];

                $resumeUrl = $this->application->resume_file
                    ? asset('storage/' . $this->application->resume_file)
                    : '';

                Mail::to($hrEmail)->send(new HRHiringMail(
                    emailSubject:      $hrSubject,
                    body:              $reason['company_summary'],
                    candidateName:     $candidateName,
                    candidateEmail:    $candidateEmail ?? 'N/A',
                    jobTitle:          $jobTitle,
                    companyName:       $companyName,
                    eventType:         $this->eventType,
                    matchScore:        $this->matchScore,
                    profile:           $profileData,
                    coverLetter:       $this->application->cover_letter ?? '',
                    applicationNumber: $this->application->application_number ?? '',
                    appliedAt:         $this->application->submitted_at?->format('M d, Y') ?? '',
                    rejectionReason:   $this->application->rejection_reason ?? '',
                    linkedinUrl:       $this->application->linkedin_url ?? '',
                    githubUrl:         $this->application->github_url ?? '',
                    portfolioUrl:      $this->application->portfolio_url ?? '',
                    resumeUrl:         $resumeUrl,
                ));

                Log::info('HiringEmail: HR email sent', [
                    'event' => $this->eventType,
                    'to'    => $hrEmail,
                ]);
            } catch (\Throwable $e) {
                Log::error('HiringEmail: failed to send HR email', [
                    'error'       => $e->getMessage(),
                    'application' => $this->application->id,
                ]);
            }
        }
    }
}

