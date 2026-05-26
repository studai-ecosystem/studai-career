<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Traits\InteractsWithAI;
use Illuminate\Support\Facades\Log;

class HiringEmailService
{
    use InteractsWithAI;

    // =========================================================================
    // CANDIDATE EMAILS
    // =========================================================================

    /**
     * Generate AI-written candidate email body for shortlisting.
     */
    public function generateCandidateShortlistedEmail(Application $application, float $matchScore): array
    {
        $jobTitle   = $application->job->title ?? 'a position';
        $company    = $application->job->company->name ?? ($application->job->company_name ?? 'the company');
        $candidate  = $application->user->name ?? 'Candidate';
        $scoreLabel = number_format($matchScore, 1);

        $systemPrompt = 'You are a professional HR communications specialist. Write warm, encouraging, professional emails on behalf of companies to job candidates. Keep emails concise (150-200 words), human, and action-oriented.';

        $prompt = <<<PROMPT
Write a shortlisting congratulations email to a job candidate.

Candidate name: {$candidate}
Job title: {$jobTitle}
Company: {$company}
Match score: {$scoreLabel}%

Requirements:
- Subject line (start with "Subject: ")
- Body below the subject
- Warm and professional tone
- Mention their match score briefly
- Tell them HR will reach out with next steps
- Close with the company name
- No placeholders in square brackets
PROMPT;

        try {
            $raw     = $this->ai($prompt, $systemPrompt, ['temperature' => 0.7, 'max_completion_tokens' => 400]);
            $subject = $this->extractSubject($raw) ?? "🎉 Congratulations {$candidate} — You've Been Shortlisted for {$jobTitle}";
            $body    = $this->stripSubjectLine($raw);

            return ['subject' => $subject, 'body' => $body];
        } catch (\Throwable $e) {
            Log::warning('HiringEmailService: AI generation failed for shortlisted email', ['error' => $e->getMessage()]);

            return [
                'subject' => "You've Been Shortlisted for {$jobTitle} at {$company}",
                'body'    => "Dear {$candidate},\n\nCongratulations! We are pleased to inform you that you have been shortlisted for the {$jobTitle} position at {$company}. Your profile matched {$scoreLabel}% of our requirements.\n\nOur HR team will be in touch shortly with details about the next steps in the hiring process.\n\nWe look forward to speaking with you!\n\nBest regards,\nThe {$company} Team",
            ];
        }
    }

    /**
     * Generate AI-written candidate email body for hiring.
     */
    public function generateCandidateHiredEmail(Application $application): array
    {
        $jobTitle  = $application->job->title ?? 'a position';
        $company   = $application->job->company->name ?? ($application->job->company_name ?? 'the company');
        $candidate = $application->user->name ?? 'Candidate';

        $systemPrompt = 'You are a professional HR communications specialist. Write warm, celebratory, professional offer announcement emails. Keep them concise (150-200 words) and action-oriented.';

        $prompt = <<<PROMPT
Write a hiring congratulations email to a job candidate who has been selected.

Candidate name: {$candidate}
Job title: {$jobTitle}
Company: {$company}

Requirements:
- Subject line (start with "Subject: ")
- Celebratory but professional tone
- Mention that a formal offer letter will follow
- Encourage them to reach out with questions
- Close with company name
- No placeholders in square brackets
PROMPT;

        try {
            $raw     = $this->ai($prompt, $systemPrompt, ['temperature' => 0.7, 'max_completion_tokens' => 400]);
            $subject = $this->extractSubject($raw) ?? "🎉 Welcome to {$company} — Offer for {$jobTitle}";
            $body    = $this->stripSubjectLine($raw);

            return ['subject' => $subject, 'body' => $body];
        } catch (\Throwable $e) {
            Log::warning('HiringEmailService: AI generation failed for hired email', ['error' => $e->getMessage()]);

            return [
                'subject' => "🎉 Congratulations — You've Been Selected for {$jobTitle} at {$company}",
                'body'    => "Dear {$candidate},\n\nWe are delighted to offer you the position of {$jobTitle} at {$company}. Your skills and experience stood out among all applicants.\n\nA formal offer letter will be sent to you shortly. Please feel free to reach out if you have any questions.\n\nWelcome aboard!\n\nBest regards,\nThe {$company} Hiring Team",
            ];
        }
    }

    /**
     * Generate AI-written HR summary email for shortlisting.
     */
    public function generateHRShortlistedEmail(Application $application, float $matchScore): array
    {
        $jobTitle   = $application->job->title ?? 'a position';
        $company    = $application->job->company->name ?? ($application->job->company_name ?? 'the company');
        $candidate  = $application->user->name ?? 'Candidate';
        $email      = $application->user->email ?? 'N/A';
        $scoreLabel = number_format($matchScore, 1);

        $systemPrompt = 'You are an AI hiring assistant writing internal HR summary emails. Be concise and professional.';

        $prompt = <<<PROMPT
Write a brief internal HR notification email about a shortlisted candidate.

Candidate: {$candidate} ({$email})
Job: {$jobTitle}
Company: {$company}
AI Match Score: {$scoreLabel}%

Requirements:
- Subject line (start with "Subject: ")
- Brief summary for the HR team
- Note the AI match score
- Recommend next action (schedule interview)
- 80-100 words max
PROMPT;

        try {
            $raw     = $this->ai($prompt, $systemPrompt, ['temperature' => 0.5, 'max_completion_tokens' => 250]);
            $subject = $this->extractSubject($raw) ?? "New Shortlisted Candidate: {$candidate} for {$jobTitle}";
            $body    = $this->stripSubjectLine($raw);

            return ['subject' => $subject, 'body' => $body];
        } catch (\Throwable $e) {
            Log::warning('HiringEmailService: AI generation failed for HR shortlisted email', ['error' => $e->getMessage()]);

            return [
                'subject' => "Shortlisted: {$candidate} for {$jobTitle}",
                'body'    => "Hi,\n\n{$candidate} ({$email}) has been shortlisted for the {$jobTitle} role with an AI match score of {$scoreLabel}%.\n\nPlease review their application and schedule the next interview step.\n\nRegards,\nStudAI Hiring Platform",
            ];
        }
    }

    /**
     * Generate AI-written HR summary email for hiring.
     */
    public function generateHRHiredEmail(Application $application): array
    {
        $jobTitle  = $application->job->title ?? 'a position';
        $company   = $application->job->company->name ?? ($application->job->company_name ?? 'the company');
        $candidate = $application->user->name ?? 'Candidate';
        $email     = $application->user->email ?? 'N/A';

        $systemPrompt = 'You are an AI hiring assistant writing internal HR confirmation emails. Be brief and action-oriented.';

        $prompt = <<<PROMPT
Write a brief internal HR notification that a candidate has been marked as hired.

Candidate: {$candidate} ({$email})
Job: {$jobTitle}
Company: {$company}

Requirements:
- Subject line (start with "Subject: ")
- Confirm the hire
- List 2-3 next onboarding action items
- 80-100 words max
PROMPT;

        try {
            $raw     = $this->ai($prompt, $systemPrompt, ['temperature' => 0.5, 'max_completion_tokens' => 250]);
            $subject = $this->extractSubject($raw) ?? "Hired: {$candidate} for {$jobTitle}";
            $body    = $this->stripSubjectLine($raw);

            return ['subject' => $subject, 'body' => $body];
        } catch (\Throwable $e) {
            Log::warning('HiringEmailService: AI generation failed for HR hired email', ['error' => $e->getMessage()]);

            return [
                'subject' => "Hire Confirmed: {$candidate} — {$jobTitle}",
                'body'    => "Hi,\n\n{$candidate} ({$email}) has been marked as hired for {$jobTitle}.\n\nNext steps:\n1. Send the formal offer letter\n2. Initiate onboarding documents\n3. Schedule day-1 orientation\n\nRegards,\nStudAI Hiring Platform",
            ];
        }
    }

    private function extractSubject(string $text): ?string
    {
        if (preg_match('/^Subject:\s*(.+)$/im', $text, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function stripSubjectLine(string $text): string
    {
        $body = preg_replace('/^Subject:.*\n?/im', '', $text);

        return trim((string) $body);
    }

    // =========================================================================
    // CANDIDATE — REJECTION
    // =========================================================================

    public function generateCandidateRejectedEmail(Application $application): array
    {
        $jobTitle  = $application->job->title ?? 'a position';
        $company   = $application->job->company->name ?? ($application->job->company_name ?? 'the company');
        $candidate = $application->user->name ?? 'Candidate';
        $reason    = $application->rejection_reason ?? null;
        $reasonLine = $reason ? "Reason: {$reason}" : '';

        $systemPrompt = 'You are a professional HR communications specialist. Write respectful, empathetic rejection emails. Keep them concise (120-150 words), kind, and encourage the candidate for future opportunities.';

        $prompt = <<<PROMPT
Write a respectful rejection email to a job candidate.

Candidate name: {$candidate}
Job title: {$jobTitle}
Company: {$company}
{$reasonLine}

Requirements:
- Subject line (start with "Subject: ")
- Empathetic and respectful tone
- Thank them for applying
- If a reason is given, briefly and professionally mention it
- Encourage them to apply for future roles
- No placeholders in square brackets
PROMPT;

        try {
            $raw     = $this->ai($prompt, $systemPrompt, ['temperature' => 0.6, 'max_completion_tokens' => 350]);
            $subject = $this->extractSubject($raw) ?? "Your Application for {$jobTitle} at {$company}";
            $body    = $this->stripSubjectLine($raw);

            return ['subject' => $subject, 'body' => $body];
        } catch (\Throwable $e) {
            Log::warning('HiringEmailService: AI generation failed for rejection email', ['error' => $e->getMessage()]);

            $reasonText = $reason ? "\n\nFeedback: {$reason}" : '';

            return [
                'subject' => "Update on Your Application — {$jobTitle} at {$company}",
                'body'    => "Dear {$candidate},\n\nThank you for taking the time to apply for the {$jobTitle} position at {$company} and for your interest in joining our team.{$reasonText}\n\nAfter careful consideration, we have decided to move forward with other candidates whose profiles more closely match our current requirements.\n\nWe encourage you to keep an eye on future openings and apply again. We wish you all the best in your job search.\n\nWarm regards,\nThe {$company} Hiring Team",
            ];
        }
    }

    // =========================================================================
    // HR EMAILS — REJECTION NOTIFICATION
    // =========================================================================

    public function generateHRRejectedEmail(Application $application): array
    {
        $jobTitle  = $application->job->title ?? 'a position';
        $company   = $application->job->company->name ?? ($application->job->company_name ?? 'the company');
        $candidate = $application->user->name ?? 'Candidate';
        $email     = $application->user->email ?? 'N/A';
        $reason    = $application->rejection_reason ?? 'Not specified';

        $systemPrompt = 'You are an AI hiring assistant writing internal HR log emails. Be very brief (50-70 words).';

        $prompt = <<<PROMPT
Write a brief internal HR log email confirming a candidate was rejected.

Candidate: {$candidate} ({$email})
Job: {$jobTitle}
Rejection reason: {$reason}

Requirements:
- Subject line (start with "Subject: ")
- Confirm rejection, state reason
- Note that the candidate has been notified by email
- 50-70 words max
PROMPT;

        try {
            $raw     = $this->ai($prompt, $systemPrompt, ['temperature' => 0.4, 'max_completion_tokens' => 180]);
            $subject = $this->extractSubject($raw) ?? "Rejected: {$candidate} — {$jobTitle}";
            $body    = $this->stripSubjectLine($raw);

            return ['subject' => $subject, 'body' => $body];
        } catch (\Throwable $e) {
            Log::warning('HiringEmailService: AI generation failed for HR rejection email', ['error' => $e->getMessage()]);

            return [
                'subject' => "Application Rejected: {$candidate} — {$jobTitle}",
                'body'    => "Hi,\n\n{$candidate} ({$email}) has been rejected for the {$jobTitle} role.\n\nReason: {$reason}\n\nThe candidate has been notified via email.\n\nRegards,\nStudAI Hiring Platform",
            ];
        }
    }
}
