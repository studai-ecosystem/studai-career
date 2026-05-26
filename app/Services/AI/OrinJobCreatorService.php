<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Orin™ Job Creator Service
 *
 * Handles the conversational AI Job Creator flow.
 * Given a company's Intelligence Profile and role inputs,
 * Orin™ generates the full JD, application form fields, and shareable link.
 */
class OrinJobCreatorService extends AIService
{
    private const SYSTEM_PROMPT = <<<PROMPT
You are Orin™, the AI talent intelligence engine for StudAI Hire, built on Azure OpenAI GPT-5.4.
You are acting as an expert talent acquisition consultant helping an employer create a job posting.
Your role is to ask smart, targeted questions in a conversational manner — never like a form.
Probe for specifics. If the employer is vague, ask follow-up questions.
Keep responses concise, professional, and friendly.
PROMPT;

    /**
     * Generate the next conversational question(s) based on current conversation state.
     */
    public function nextQuestion(array $conversationHistory, array $companyProfile, string $roleName, string $roleDescription): string
    {
        $companyContext = $this->buildCompanyContext($companyProfile);

        $messages = [
            ['role' => 'system', 'content' => self::SYSTEM_PROMPT . "\n\n" . $companyContext],
            ['role' => 'system', 'content' => "The employer wants to create a job for: {$roleName}. Brief description: {$roleDescription}"],
            ['role' => 'system', 'content' => "You need to collect: salary range, work mode, experience level, must-have vs nice-to-have skills, application open/close dates, evaluation start date, finalisation date, target hire count, portfolio/GitHub requirements, and any mandatory screening questions. Ask naturally one or two topics at a time."],
        ];

        foreach ($conversationHistory as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }

        try {
            return $this->callAzureOpenAI($messages, ['temperature' => 0.7, 'max_completion_tokens' => 500]);
        } catch (\Exception $e) {
            Log::error('OrinJobCreator::nextQuestion failed', ['error' => $e->getMessage()]);
            return "I need a bit more information. Could you tell me the salary range and preferred work mode (remote/hybrid/on-site) for this role?";
        }
    }

    /**
     * Extract structured job data from the completed conversation.
     */
    public function extractJobData(array $conversationHistory, string $roleName, string $roleDescription): array
    {
        $transcript = collect($conversationHistory)
            ->map(fn($m) => strtoupper($m['role']) . ': ' . $m['content'])
            ->implode("\n");

        $extractionPrompt = <<<PROMPT
From the conversation below, extract the following fields into a JSON object.
Use null for any field not mentioned. Return ONLY valid JSON.

Fields to extract:
- salary_min (integer, in INR per year)
- salary_max (integer, in INR per year)
- salary_currency (string, default "INR")
- location_type (enum: "remote"|"hybrid"|"onsite")
- experience_level (enum: "entry"|"junior"|"mid"|"senior"|"lead"|"executive")
- required_skills (array of strings)
- preferred_skills (array of strings)
- open_date (date YYYY-MM-DD or null)
- close_date (date YYYY-MM-DD or null)
- eval_start_date (date YYYY-MM-DD or null)
- final_date (date YYYY-MM-DD or null)
- target_hire_count (integer, default 1)
- requires_portfolio (boolean)
- requires_github (boolean)
- requires_work_sample (boolean)
- mandatory_screening_questions (array of strings)
- employment_type (enum: "full_time"|"part_time"|"contract"|"internship")

CONVERSATION:
{$transcript}
PROMPT;

        try {
            $response = $this->callAzureOpenAI([
                ['role' => 'system', 'content' => 'You are a JSON extractor. Return only valid JSON, no markdown.'],
                ['role' => 'user', 'content' => $extractionPrompt],
            ], ['temperature' => 0.1, 'max_completion_tokens' => 1000]);

            $json = trim(preg_replace('/^```json\s*|\s*```$/m', '', $response));
            return json_decode($json, true) ?? [];
        } catch (\Exception $e) {
            Log::error('OrinJobCreator::extractJobData failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Generate a full, optimised job description from the collected data.
     */
    public function generateJobDescription(string $roleName, string $roleDescription, array $jobData, array $companyProfile): string
    {
        $companyContext = $this->buildCompanyContext($companyProfile);
        $skills = implode(', ', $jobData['required_skills'] ?? []);
        $preferredSkills = implode(', ', $jobData['preferred_skills'] ?? []);
        $expLevel = $jobData['experience_level'] ?? 'mid';
        $workMode = $jobData['location_type'] ?? 'hybrid';

        $prompt = <<<PROMPT
You are Orin™, writing a job description for a {$roleName} role.

Company context:
{$companyContext}

Role brief from employer: {$roleDescription}

Collected details:
- Experience level: {$expLevel}
- Work mode: {$workMode}
- Required skills: {$skills}
- Preferred skills: {$preferredSkills}

Write a compelling, ATS-optimised job description with these sections:
1. About the Role (2-3 sentences, engaging)
2. What You'll Do (5-7 bullet points, specific responsibilities)
3. What We're Looking For (must-haves then nice-to-haves)
4. What We Offer (benefits, culture, growth opportunities)
5. About [Company Name] (1 paragraph from the company context)

Use active voice. Be specific, not generic. Avoid corporate buzzword padding.
Format in clean Markdown.
PROMPT;

        try {
            return $this->callAzureOpenAI([
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user', 'content' => $prompt],
            ], ['temperature' => 0.7, 'max_completion_tokens' => 2000]);
        } catch (\Exception $e) {
            Log::error('OrinJobCreator::generateJobDescription failed', ['error' => $e->getMessage()]);
            return "## {$roleName}\n\n{$roleDescription}";
        }
    }

    /**
     * Generate dynamic application form fields tailored to the role.
     */
    public function generateApplicationFormFields(string $roleName, array $jobData, array $companyProfile): array
    {
        $skills = implode(', ', $jobData['required_skills'] ?? []);
        $locationTypeValue = $jobData['location_type'] ?? 'hybrid';

        $prompt = <<<PROMPT
You are Orin™. Generate a dynamic application form for a {$roleName} role.
Required skills: {$skills}
Work mode: {$locationTypeValue}

Return a JSON array of form fields. Each field has:
- name (snake_case string)
- label (human readable)
- type (text|textarea|select|checkbox|url|file)
- required (boolean)
- options (array of strings for select type, or null)
- placeholder (string or null)

Include: standard fields (name, email, phone, LinkedIn) + 3-5 role-specific fields.
Return ONLY valid JSON array, no markdown.
PROMPT;

        try {
            $response = $this->callAzureOpenAI([
                ['role' => 'system', 'content' => 'Return only valid JSON, no markdown.'],
                ['role' => 'user', 'content' => $prompt],
            ], ['temperature' => 0.3, 'max_completion_tokens' => 1000]);

            $json = trim(preg_replace('/^```json\s*|\s*```$/m', '', $response));
            return json_decode($json, true) ?? $this->defaultFormFields();
        } catch (\Exception $e) {
            Log::error('OrinJobCreator::generateApplicationFormFields failed', ['error' => $e->getMessage()]);
            return $this->defaultFormFields();
        }
    }

    /**
     * Create and persist the full job posting from Orin™ conversation output.
     */
    public function createJob(User $employer, string $roleName, string $roleDescription, array $jobData, array $companyProfile): Job
    {
        $generatedJD = $this->generateJobDescription($roleName, $roleDescription, $jobData, $companyProfile);
        $formFields = $this->generateApplicationFormFields($roleName, $jobData, $companyProfile);

        $company = $employer->company;

        return Job::create([
            'company_id'          => $company?->id,
            'title'               => $roleName,
            'slug'                => Str::slug($roleName . '-' . Str::random(6)),
            'description'         => $generatedJD,
            'location'            => $company?->headquarters ?? 'India',
            'work_mode'           => $jobData['location_type'] ?? $jobData['work_mode'] ?? 'hybrid',
            'employment_type'     => $jobData['employment_type'] ?? 'full_time',
            'experience_level'    => $jobData['experience_level'] ?? 'mid',
            'salary_min'          => $jobData['salary_min'] ?? null,
            'salary_max'          => $jobData['salary_max'] ?? null,
            'required_skills'     => $jobData['required_skills'] ?? [],
            'open_date'           => $jobData['open_date'] ?? now()->toDateString(),
            'close_date'          => $jobData['close_date'] ?? null,
            'eval_start_date'     => $jobData['eval_start_date'] ?? null,
            'final_date'          => $jobData['final_date'] ?? null,
            'target_hire_count'   => $jobData['target_hire_count'] ?? 1,
            'requires_portfolio'  => $jobData['requires_portfolio'] ?? false,
            'requires_github'     => $jobData['requires_github'] ?? false,
            'requires_work_sample' => $jobData['requires_work_sample'] ?? false,
            'mandatory_screening_questions' => $jobData['mandatory_screening_questions'] ?? [],
            'orin_generated_jd'   => ['raw' => $generatedJD, 'generated_at' => now()->toIso8601String()],
            'orin_application_form_fields' => $formFields,
            'application_link_token' => Str::random(12),
            'application_phase'   => 'open',
            'status'              => 'published',
            'published_at'        => now(),
            'expires_at'          => ($jobData['close_date'] ?? null) ? \Carbon\Carbon::parse($jobData['close_date'])->addDay() : now()->addDays(30),
        ]);
    }

    private function buildCompanyContext(array $companyProfile): string
    {
        $name    = $companyProfile['name'] ?? 'the company';
        $industry = $companyProfile['industry'] ?? 'technology';
        $culture = is_array($companyProfile['culture_values'] ?? null)
            ? implode(', ', $companyProfile['culture_values'])
            : ($companyProfile['culture_values'] ?? 'collaborative');

        return "Company: {$name}. Industry: {$industry}. Culture values: {$culture}.";
    }

    private function defaultFormFields(): array
    {
        return [
            ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'text', 'required' => true, 'options' => null, 'placeholder' => 'Your full name'],
            ['name' => 'email', 'label' => 'Email Address', 'type' => 'text', 'required' => true, 'options' => null, 'placeholder' => 'your@email.com'],
            ['name' => 'phone', 'label' => 'Phone Number', 'type' => 'text', 'required' => true, 'options' => null, 'placeholder' => '+91 XXXXX XXXXX'],
            ['name' => 'linkedin_url', 'label' => 'LinkedIn Profile', 'type' => 'url', 'required' => false, 'options' => null, 'placeholder' => 'https://linkedin.com/in/...'],
            ['name' => 'resume', 'label' => 'Resume / CV', 'type' => 'file', 'required' => true, 'options' => null, 'placeholder' => null],
        ];
    }
}
