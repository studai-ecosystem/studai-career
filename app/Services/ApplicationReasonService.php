<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Services\AI\AIService;
use Illuminate\Support\Facades\Log;

/**
 * Generates AI-powered, specific reasons for each application status change.
 * Returns a structured payload used by all hiring emails and stored on the application.
 *
 * Output shape:
 *   student_reason  — personalised explanation sent to the candidate
 *   company_summary — internal HR summary of the decision
 *   student_tip     — one actionable tip for the candidate
 *   tone            — 'celebratory' | 'encouraging' | 'empathetic' | 'neutral'
 */
class ApplicationReasonService
{
    public function __construct(private readonly AIService $ai) {}

    /**
     * Generate a structured reason payload for a status change.
     *
     * @param  Application $application  Eager-loaded with user.profile, job.company
     * @param  string      $newStatus    shortlisted | interviewed | hired | rejected
     * @return array{student_reason: string, company_summary: string, student_tip: string, tone: string}
     */
    public function generate(Application $application, string $newStatus): array
    {
        $profile    = $application->user?->profile;
        $job        = $application->job;
        $company    = $job->company;
        $candidate  = $application->user?->name ?? 'Candidate';
        $jobTitle   = $job->title ?? 'the position';
        $companyName = $company?->name ?? 'the company';
        $matchScore = number_format((float) ($application->final_rank_score ?? $application->match_score ?? 0), 1);

        // Build profile context string
        $skills = '';
        if (is_array($profile?->skills)) {
            $pillParts = [];
            foreach ($profile->skills as $skill) {
                if (is_array($skill)) {
                    $name        = $skill['name'] ?? $skill['skill'] ?? '';
                    $proficiency = $skill['proficiency'] ?? $skill['level'] ?? '';
                    $pillParts[] = $name . ($proficiency ? " ({$proficiency})" : '');
                } elseif (is_string($skill)) {
                    $pillParts[] = $skill;
                }
            }
            $skills = implode(', ', array_filter($pillParts));
        } elseif (is_string($profile?->skills)) {
            $skills = $profile->skills;
        }

        $experience = '';
        if (is_array($profile?->experience)) {
            $expParts = [];
            foreach (array_slice($profile->experience, 0, 3) as $exp) {
                $t = $exp['title'] ?? $exp['position'] ?? '';
                $c = $exp['company'] ?? '';
                if ($t) {
                    $expParts[] = $t . ($c ? " at {$c}" : '');
                }
            }
            $experience = implode('; ', $expParts);
        }

        $education = '';
        if (is_array($profile?->education)) {
            $eduParts = [];
            foreach (array_slice($profile->education, 0, 2) as $edu) {
                $d = $edu['degree'] ?? $edu['qualification'] ?? '';
                $s = $edu['institution'] ?? $edu['school'] ?? '';
                if ($d || $s) {
                    $eduParts[] = $d . ($s ? " from {$s}" : '');
                }
            }
            $education = implode('; ', $eduParts);
        }

        $rejectionReason = $application->rejection_reason ?? '';
        $headline        = $profile?->headline ?? '';
        $workPref        = $profile?->work_preference ?? '';
        $noticePeriod    = $profile?->notice_period ?? '';

        // Job requirements context
        $jobDesc        = mb_substr($job->description ?? '', 0, 600);
        $requiredSkills = is_array($job->required_skills)
            ? implode(', ', $job->required_skills)
            : ($job->required_skills ?? '');
        $expLevel       = $job->experience_level ?? '';

        $statusLabels = [
            'shortlisted'  => 'shortlisted (selected for further review)',
            'interviewed'  => 'selected to proceed to interview stage',
            'hired'        => 'selected and hired for the role',
            'rejected'     => 'not selected for this position',
        ];
        $statusLabel = $statusLabels[$newStatus] ?? $newStatus;

        $systemPrompt = 'You are an expert HR communications AI. You generate specific, personalised, non-generic email content for job application status changes. You always reference actual candidate data and job details — never use placeholders. Return ONLY valid JSON, no markdown fences.';

        // Pre-extract values to avoid complex expressions inside heredoc
        $rejectionContext = $rejectionReason ? "HR rejection note: {$rejectionReason}" : '';
        $toneInstructions = match ($newStatus) {
            'shortlisted' => 'Tone: warm, encouraging. Explain specifically which skills or experience matched. End the student_tip with advice to prepare for the interview.',
            'interviewed' => 'Tone: professional, positive. Explain what qualified them and what to expect (3-7 days). Tip: how to stand out post-interview.',
            'hired'       => 'Tone: celebratory, enthusiastic. Be specific about why they were chosen. Tip: onboarding advice.',
            'rejected'    => 'Tone: empathetic, constructive. Give a SPECIFIC gap reason (not generic). Tip: one concrete improvement action.',
            default       => 'Tone: neutral and professional.',
        };

        $prompt = <<<PROMPT
Generate a JSON response for an application status change.

=== CANDIDATE ===
Name: {$candidate}
Headline: {$headline}
Skills: {$skills}
Recent Experience: {$experience}
Education: {$education}
Work Preference: {$workPref}
Notice Period: {$noticePeriod}
AI Match Score: {$matchScore}%

=== JOB ===
Title: {$jobTitle}
Company: {$companyName}
Required Skills: {$requiredSkills}
Experience Level: {$expLevel}
Job Description (excerpt): {$jobDesc}

=== STATUS CHANGE ===
New Status: {$statusLabel}
{$rejectionContext}

=== INSTRUCTIONS ===
{$toneInstructions}

Return ONLY this JSON structure (no extra text):
{
  "student_reason": "2-3 sentence personalised message to {$candidate} explaining this decision with specific references to their skills/experience/match",
  "company_summary": "1-2 sentence internal HR note summarising the decision for audit",
  "student_tip": "one specific, actionable improvement tip relevant to their profile and this job",
  "tone": "celebratory|encouraging|empathetic|neutral"
}
PROMPT;

        try {
            $raw = $this->ai->generateText($prompt, $systemPrompt, [
                'max_tokens'   => 500,
                'temperature'  => 0.65,
                'skip_cache'   => true,
            ]);

            // Strip any accidental markdown fences
            $json = preg_replace('/```(?:json)?\s*/i', '', $raw ?? '');
            $json = trim((string) $json, " `\n");

            $decoded = json_decode($json, true);

            if (is_array($decoded) && isset($decoded['student_reason'])) {
                return $decoded;
            }

            Log::warning('ApplicationReasonService: JSON parse failed, using fallback', [
                'raw'    => $raw,
                'status' => $newStatus,
            ]);
        } catch (\Throwable $e) {
            Log::error('ApplicationReasonService: AI call failed', [
                'error'          => $e->getMessage(),
                'application_id' => $application->id,
                'status'         => $newStatus,
            ]);
        }

        return $this->fallback($newStatus, $candidate, $jobTitle, $companyName, $matchScore, $rejectionReason);
    }

    /**
     * Sensible fallbacks if AI is unavailable.
     */
    private function fallback(
        string $status,
        string $candidate,
        string $jobTitle,
        string $companyName,
        string $matchScore,
        string $rejectionReason
    ): array {
        return match ($status) {
            'shortlisted' => [
                'student_reason'  => "Hi {$candidate}, your profile stood out for the {$jobTitle} role at {$companyName} with an AI match score of {$matchScore}%. Your skills and experience are a strong fit for what the team is looking for.",
                'company_summary' => "{$candidate} shortlisted with {$matchScore}% match score. Profile aligns well with job requirements.",
                'student_tip'     => 'Review the job description carefully and prepare 2–3 examples of your most relevant work to discuss in the interview.',
                'tone'            => 'encouraging',
            ],
            'interviewed' => [
                'student_reason'  => "Congratulations {$candidate}! You've been selected to interview for {$jobTitle} at {$companyName}. Your background and {$matchScore}% match score impressed the hiring team.",
                'company_summary' => "{$candidate} advancing to interview. Match score: {$matchScore}%.",
                'student_tip'     => 'Research the company culture and prepare thoughtful questions to ask the interviewer — it shows genuine interest.',
                'tone'            => 'encouraging',
            ],
            'hired' => [
                'student_reason'  => "🎉 {$candidate}, you've been selected for {$jobTitle} at {$companyName}! Your skills, experience, and overall profile were exactly what the team was looking for. Welcome aboard!",
                'company_summary' => "{$candidate} hired for {$jobTitle}. Initiate onboarding process.",
                'student_tip'     => 'Reach out to HR to confirm your start date and complete any onboarding documents as early as possible.',
                'tone'            => 'celebratory',
            ],
            'rejected' => [
                'student_reason'  => "Thank you for applying for {$jobTitle} at {$companyName}, {$candidate}. After careful review" . ($rejectionReason ? ", {$rejectionReason}" : ', we found that other candidates were a closer match for this specific role at this time') . '.',
                'company_summary' => "{$candidate} rejected for {$jobTitle}." . ($rejectionReason ? " Reason: {$rejectionReason}" : '') . ' Candidate notified.',
                'student_tip'     => 'Keep building on your skills and apply to other roles that match your profile on StudAI Hire — new opportunities are posted daily.',
                'tone'            => 'empathetic',
            ],
            default => [
                'student_reason'  => "Your application for {$jobTitle} at {$companyName} has been updated.",
                'company_summary' => "Status updated for {$candidate}.",
                'student_tip'     => 'Check your application dashboard for the latest status.',
                'tone'            => 'neutral',
            ],
        };
    }
}
