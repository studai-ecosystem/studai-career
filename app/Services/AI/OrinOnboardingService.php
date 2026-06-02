<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\Company;
use App\Models\CompanyIntelligenceProfile;
use Illuminate\Support\Facades\Log;

/**
 * Orin™ Employer Onboarding Service
 *
 * Drives the conversational company onboarding interview.
 * Extracts structured Company Intelligence Profile from dialogue.
 */
class OrinOnboardingService extends AIService
{
    private const SYSTEM_PROMPT = <<<PROMPT
You are Orin™, the AI talent intelligence engine for StudAI Hire, powered by Azure OpenAI GPT-5.4.
You are conducting a friendly, conversational onboarding interview with an employer who has just joined the platform.
Your goal is to build a comprehensive Company Intelligence Profile through natural dialogue — NOT a form.

You need to learn about:
1. Company basics: name, industry, size, headcount, founded year, website, CIN/GST
2. Team structure and current headcount by function
3. Work culture: collaborative, autonomous, fast-paced, structured, startup vs corporate feel
4. Compensation philosophy: competitive, below-market, equity-heavy, performance-linked
5. Salary bands by level: junior, mid, senior, lead
6. Work mode: remote, hybrid, on-site, location preferences
7. Hiring frequency: one-time, seasonal, ongoing, bulk hiring
8. Top 3 traits of their best-performing employees (probe for specifics)
9. Biggest current hiring pain points (probe for specifics)
10. Any compliance or regulatory requirements (POSH, FCRA, background checks)
11. Preferred communication style with candidates

Rules:
- Ask only 1-2 topics per message. Never dump all questions at once.
- If the employer gives a vague answer, probe with a follow-up.
- Be warm, professional, and encouraging.
- When you have collected enough (at least 7 of the 11 topics), conclude warmly by telling the employer they can now click the "Finish Setup" button below to save their profile. Then, on its own line at the very end of that message, append the exact token [[READY]].
- Only ever output the [[READY]] token once you genuinely have at least 7 topics.
- Never ask for information already provided in this conversation.
PROMPT;

    /**
     * Sentinel token Orin appends when the profile has enough data to finalize.
     */
    public const READY_TOKEN = '[[READY]]';

    /**
     * Drive the next turn in the onboarding conversation.
     */
    public function nextMessage(array $history, Company $company): string
    {
        $messages = [
            ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
            ['role' => 'system', 'content' => "Company account details: Name={$company->name}, Email domain registered."],
        ];

        // F7: compact long dialogues to cap token growth / cost.
        $windowed = $this->compactConversation($history, keepRecent: 6, triggerAfter: 6);

        foreach ($windowed as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }

        // If no history, start the interview
        if (empty($history)) {
            $messages[] = ['role' => 'user', 'content' => 'Hello, I just joined.'];
        }

        try {
            return $this->callAzureOpenAI($messages, ['temperature' => 0.7, 'max_completion_tokens' => 400]);
        } catch (\Exception $e) {
            Log::error('OrinOnboarding::nextMessage failed', ['error' => $e->getMessage()]);
            return "Welcome to StudAI Hire! I'm Orin™, your AI talent consultant. Let's start building your company profile. What industry is {$company->name} in, and roughly how many people are on your team right now?";
        }
    }

    /**
     * Extract structured Company Intelligence Profile from completed conversation.
     */
    public function extractProfile(array $history, Company $company): CompanyIntelligenceProfile
    {
        $transcript = collect($history)
            ->map(fn($m) => strtoupper($m['role']) . ': ' . $m['content'])
            ->implode("\n");

        $extractionMessages = [
            ['role' => 'system', 'content' => 'Extract company intelligence profile from the conversation below. Return ONLY valid JSON with these keys: industry, company_size (micro/small/medium/large/enterprise), headcount (integer), founded_year, cin_gst, website, work_culture, work_mode_preference (remote/hybrid/onsite), top_performer_traits (array of strings), salary_bands (object: junior/mid/senior/lead each with min and max in INR per year), compensation_philosophy, pain_points (array), preferred_candidate_communication, hiring_frequency (one-time/seasonal/ongoing/bulk), compliance_requirements (array). Use null for anything not mentioned.'],
            ['role' => 'user', 'content' => $transcript],
        ];

        $json = null;
        try {
            $raw = $this->callAzureOpenAI($extractionMessages, ['temperature' => 0.1, 'max_completion_tokens' => 1000, 'json_mode' => true]);
            $raw = preg_replace('/```json|```/', '', $raw);
            $json = json_decode(trim($raw), true);
        } catch (\Exception $e) {
            Log::error('OrinOnboarding::extractProfile failed', ['error' => $e->getMessage()]);
        }

        $data = $json ?? [];

        // Calculate completeness (out of 11 fields)
        $filled = collect([
            $data['industry'] ?? null,
            $data['headcount'] ?? null,
            $data['work_culture'] ?? null,
            $data['work_mode_preference'] ?? null,
            $data['compensation_philosophy'] ?? null,
            $data['top_performer_traits'] ?? null,
            $data['salary_bands'] ?? null,
            $data['pain_points'] ?? null,
            $data['hiring_frequency'] ?? null,
            $data['preferred_candidate_communication'] ?? null,
            $data['compliance_requirements'] ?? null,
        ])->filter()->count();

        $completeness = (int) round(($filled / 11) * 100);

        return CompanyIntelligenceProfile::updateOrCreate(
            ['company_id' => $company->id],
            [
                'onboarding_conversation'           => $history,
                'industry'                          => $data['industry'] ?? null,
                'company_size'                      => $data['company_size'] ?? null,
                'headcount'                         => $data['headcount'] ?? null,
                'founded_year'                      => $data['founded_year'] ?? null,
                'cin_gst'                           => $data['cin_gst'] ?? null,
                'website'                           => $data['website'] ?? ($company->website ?? null),
                'work_culture'                      => $data['work_culture'] ?? null,
                'work_mode_preference'              => $data['work_mode_preference'] ?? null,
                'top_performer_traits'              => $data['top_performer_traits'] ?? null,
                'salary_bands'                      => $data['salary_bands'] ?? null,
                'compensation_philosophy'           => $data['compensation_philosophy'] ?? null,
                'pain_points'                       => $data['pain_points'] ?? null,
                'preferred_candidate_communication' => $data['preferred_candidate_communication'] ?? null,
                'hiring_frequency'                  => $data['hiring_frequency'] ?? null,
                'compliance_requirements'           => $data['compliance_requirements'] ?? null,
                'onboarding_complete'               => true,
                'completeness_score'                => $completeness,
                'last_enriched_at'                  => now(),
            ]
        );
    }
}
