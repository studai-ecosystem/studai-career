<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\CheckAndAwardSkillBadges;
use App\Models\Company;
use App\Models\InterviewSession;
use App\Models\Job;
use App\Services\AI\AIService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MockInterviewService
{
    protected AIService $aiService;
    private VantageEvaluatorService $evaluator;
    private VantageExecutiveService $executive;

    public function __construct(
        AIService $aiService,
        VantageEvaluatorService $evaluator,
        VantageExecutiveService $executive,
    ) {
        $this->aiService = $aiService;
        $this->evaluator = $evaluator;
        $this->executive = $executive;
    }
    
    /**
     * Generate interview questions for a specific job/role
     */
    public function generateQuestions($jobTitle, $level, ?Company $company = null, $count = 10)
    {
        $cacheKey = "interview_questions_v5_" . md5($jobTitle . $level . ($company?->id ?? '') . $count);

        // Serve valid cached AI results (skip cache by setting env INTERVIEW_SKIP_CACHE=true)
        if (!config('app.debug') || !env('INTERVIEW_SKIP_CACHE', false)) {
            $cached = Cache::get($cacheKey);
            if (
                is_array($cached) &&
                (count($cached['behavioral'] ?? []) + count($cached['technical'] ?? []) + count($cached['situational'] ?? [])) > 0
            ) {
                return $cached;
            }
        }

        $companyContext = '';
        if ($company) {
            $companyContext = " at {$company->name}";
            if ($company->industry) {
                $companyContext .= " in the {$company->industry} industry";
            }
        }

        $behavioral  = (int) ceil($count * 0.4);
        $technical   = (int) ceil($count * 0.35);
        $situational = max(1, $count - $behavioral - $technical);

        // Concise prompt → faster response, fewer tokens, less chance of timeout
        $prompt = <<<PROMPT
Generate {$count} 2025 interview questions for a {$level} {$jobTitle}{$companyContext}.
Include {$behavioral} behavioral, {$technical} technical, {$situational} situational.
Return ONLY raw JSON (no markdown, no explanation):
{"behavioral":[{"question":"...","category":"leadership|teamwork|problem-solving|growth|communication","difficulty":"easy|medium|hard"}],"technical":[{"question":"...","topic":"...","difficulty":"easy|medium|hard"}],"situational":[{"question":"...","scenario":"...","difficulty":"easy|medium|hard"}]}
PROMPT;

        $systemPrompt = 'You are a senior recruiter. Return ONLY valid JSON. No markdown. No extra text.';

        try {
            $response = $this->aiService->generateText(
                $prompt,
                $systemPrompt,
                ['skip_cache' => true, 'timeout' => 25, 'max_tokens' => 2000]
            );

            // Strip markdown code fences if the model added them
            $json = preg_replace('/```(?:json)?\s*([\s\S]*?)\s*```/i', '$1', $response);
            $json = trim($json);

            // Extract the JSON object even if there is surrounding text
            if (preg_match('/(\{[\s\S]*\})/m', $json, $matches)) {
                $json = $matches[1];
            }

            $questions = json_decode($json, true);

            if (is_array($questions)) {
                $questions['behavioral']  = array_values($questions['behavioral']  ?? []);
                $questions['technical']   = array_values($questions['technical']   ?? []);
                $questions['situational'] = array_values($questions['situational'] ?? []);

                $total = count($questions['behavioral']) + count($questions['technical']) + count($questions['situational']);
                if ($total > 0) {
                    Cache::put($cacheKey, $questions, 7200); // cache 2h
                    return $questions;
                }
            }

            Log::warning('MockInterviewService: unparseable AI JSON, using fallback', [
                'job_title' => $jobTitle,
                'snippet'   => substr($response, 0, 300),
            ]);
        } catch (\Exception $e) {
            Log::error('MockInterviewService: generateQuestions failed: ' . $e->getMessage(), [
                'job_title' => $jobTitle,
            ]);
        }

        return $this->getFallbackQuestions($jobTitle, $level);
    }
    
    /**
     * Evaluate an interview answer using AI
     */
    public function evaluateAnswer($question, $answer, $context = [])
    {
        $prompt = "Evaluate this interview answer:\n\n";
        $prompt .= "Question: {$question}\n";
        $prompt .= "Answer: {$answer}\n\n";
        
        if (!empty($context['job_title'])) {
            $prompt .= "Job Role: {$context['job_title']}\n";
        }
        
        if (!empty($context['experience_level'])) {
            $prompt .= "Experience Level: {$context['experience_level']}\n";
        }
        
        $prompt .= "\nProvide feedback as JSON:
{
    \"score\": 0-100,
    \"strengths\": [\"point1\", \"point2\"],
    \"areas_for_improvement\": [\"point1\", \"point2\"],
    \"suggestions\": [\"tip1\", \"tip2\"],
    \"star_method_usage\": {\"situation\": true/false, \"task\": true/false, \"action\": true/false, \"result\": true/false},
    \"overall_feedback\": \"2 sentences max: one strength, one improvement\"
}";
        
        $systemPrompt = "You are an experienced interview coach. Evaluate answers constructively. Keep overall_feedback to 2 sentences maximum — one sentence on what was good, one on the key improvement needed. Be direct and specific.";

        // E11: enforce a per-session AI cost budget. When the session has spent
        // its soft budget we stop making new AI calls and fall back to the
        // heuristic evaluation so a single session cannot run up unbounded cost.
        $sessionId = isset($context['session_id']) ? (int) $context['session_id'] : null;

        if ($sessionId !== null && \App\Services\AI\AICostMeter::sessionBudgetExceeded($sessionId)) {
            Log::warning('MockInterview session AI budget exceeded, using heuristic evaluation', [
                'session_id' => $sessionId,
            ]);

            return $this->getBasicEvaluation($answer);
        }

        try {
            $response = $this->aiService->generateText($prompt, $systemPrompt);

            if ($sessionId !== null) {
                \App\Services\AI\AICostMeter::recordSession(
                    $sessionId,
                    (float) config('ai.cost.per_answer_eval_usd', 0.05)
                );
            }

            $evaluation = json_decode($response, true);

            if (!$evaluation) {
                return $this->getBasicEvaluation($answer);
            }

            return $evaluation;
        } catch (\Exception $e) {
            Log::error('Failed to evaluate answer: ' . $e->getMessage());
            return $this->getBasicEvaluation($answer);
        }
    }
    
    /**
     * Format answer using STAR method
     */
    public function formatWithSTAR($rawAnswer)
    {
        $prompt = "Reformat this interview answer using the STAR method (Situation, Task, Action, Result):\n\n{$rawAnswer}\n\n";
        $prompt .= "Return as JSON:
{
    \"situation\": \"context and background\",
    \"task\": \"challenge or responsibility\",
    \"action\": \"what you did\",
    \"result\": \"outcome and impact\",
    \"formatted_answer\": \"complete formatted paragraph\"
}";
        
        $systemPrompt = "You are an interview coach helping candidates structure their answers clearly using the STAR method.";
        
        try {
            $response = $this->aiService->generateText($prompt, $systemPrompt);
            return json_decode($response, true);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get common questions for a role
     */
    public function getCommonQuestions($jobTitle, $limit = 20)
    {
        $cacheKey = "common_questions_" . md5($jobTitle);
        
        return Cache::remember($cacheKey, 86400, function () use ($jobTitle, $limit) {
            // This would ideally pull from a database of frequently asked questions
            // For now, we'll generate with AI
            
            $prompt = "List the {$limit} most commonly asked interview questions for a {$jobTitle} position.";
            $prompt .= "\n\nReturn as JSON array of objects:
[
    {
        \"question\": \"...\",
        \"type\": \"behavioral/technical/situational\",
        \"frequency\": \"very_common/common/occasional\",
        \"tips\": \"brief tip for answering\"
    }
]";
            
            try {
                $response = $this->aiService->generateText($prompt, "You are an interview expert with deep knowledge of hiring practices.");
                return json_decode($response, true) ?? [];
            } catch (\Exception $e) {
                return $this->getGenericCommonQuestions();
            }
        });
    }
    
    /**
     * Get interview tips for specific role
     */
    public function getInterviewTips($jobTitle, $company = null)
    {
        $prompt = "Provide 10 specific interview tips for a candidate interviewing for a {$jobTitle} position";
        
        if ($company) {
            $prompt .= " at {$company->name}";
        }
        
        $prompt .= ".\n\nReturn as JSON array: [{\"tip\": \"...\", \"category\": \"preparation/presentation/technical/behavioral\"}]";
        
        try {
            $response = $this->aiService->generateText($prompt, "You are a career coach specializing in interview preparation.");
            return json_decode($response, true) ?? $this->getGenericTips();
        } catch (\Exception $e) {
            return $this->getGenericTips();
        }
    }
    
    /**
     * Generate salary negotiation script
     */
    public function getSalaryNegotiationGuide($jobTitle, $currentSalary, $targetSalary, $context = [])
    {
        $prompt = "Create a salary negotiation strategy for a {$jobTitle} position.\n";
        $prompt .= "Current/Expected Salary: ₹{$currentSalary}\n";
        $prompt .= "Target Salary: ₹{$targetSalary}\n\n";
        
        if (!empty($context['years_experience'])) {
            $prompt .= "Experience: {$context['years_experience']} years\n";
        }
        
        if (!empty($context['unique_skills'])) {
            $prompt .= "Unique Skills: " . implode(', ', $context['unique_skills']) . "\n";
        }
        
        $prompt .= "\nProvide as JSON:
{
    \"opening_statement\": \"how to broach the topic\",
    \"justification_points\": [\"reason1\", \"reason2\", \"reason3\"],
    \"counter_responses\": [
        {\"objection\": \"...\", \"response\": \"...\"}
    ],
    \"negotiation_tactics\": [\"tactic1\", \"tactic2\"],
    \"alternative_benefits\": [\"benefit1\", \"benefit2\"],
    \"sample_script\": \"complete conversation example\"
}";
        
        try {
            $response = $this->aiService->generateText($prompt, "You are a salary negotiation expert helping professionals maximize their compensation.");
            return json_decode($response, true);
        } catch (\Exception $e) {
            return $this->getGenericNegotiationGuide();
        }
    }
    
    /**
     * Practice session - generate follow-up questions
     */
    public function generateFollowUp($originalQuestion, $userAnswer)
    {
        $prompt = "Based on this interview answer, generate 2-3 relevant follow-up questions:\n\n";
        $prompt .= "Original Question: {$originalQuestion}\n";
        $prompt .= "Candidate's Answer: {$userAnswer}\n\n";
        $prompt .= "Return as JSON array: [{\"question\": \"...\", \"purpose\": \"clarify/probe_deeper/test_knowledge\"}]";
        
        try {
            $response = $this->aiService->generateText($prompt, "You are an interviewer conducting a thorough interview.");
            return json_decode($response, true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Fallback questions when AI fails
     */
    protected function getFallbackQuestions($jobTitle, $level)
    {
        return [
            'behavioral' => [
                ['question' => 'Tell me about yourself and your background as a ' . $jobTitle . '.', 'category' => 'general', 'difficulty' => 'easy'],
                ['question' => 'Describe the most challenging project you have worked on. What was your role and what did you learn?', 'category' => 'problem-solving', 'difficulty' => 'medium'],
                ['question' => 'How do you handle conflicts with team members? Give a specific example.', 'category' => 'teamwork', 'difficulty' => 'medium'],
                ['question' => 'Tell me about a time you had to learn a new technology or tool quickly. How did you approach it?', 'category' => 'growth', 'difficulty' => 'medium'],
                ['question' => 'Describe a situation where you had to influence a decision without having direct authority.', 'category' => 'leadership', 'difficulty' => 'hard'],
            ],
            'technical' => [
                ['question' => 'What are the key technical skills you bring to a ' . $jobTitle . ' role, and how do you stay updated with industry changes?', 'topic' => 'skills', 'difficulty' => 'easy'],
                ['question' => 'Describe a technical challenge you faced recently and walk me through how you solved it.', 'topic' => 'problem-solving', 'difficulty' => 'medium'],
                ['question' => 'How are you incorporating AI tools into your workflow as a ' . $jobTitle . '?', 'topic' => 'AI & modern tools', 'difficulty' => 'medium'],
                ['question' => 'How do you approach code/work quality, testing, and documentation in your projects?', 'topic' => 'quality', 'difficulty' => 'medium'],
            ],
            'situational' => [
                ['question' => 'You are given a project with an extremely tight deadline and limited resources. How do you prioritize and deliver?', 'scenario' => 'time management', 'difficulty' => 'medium'],
                ['question' => 'Your manager asks you to implement a solution you believe is not the best approach. What do you do?', 'scenario' => 'conflict', 'difficulty' => 'hard'],
                ['question' => 'A key team member suddenly leaves mid-project. How do you ensure delivery without compromising quality?', 'scenario' => 'team disruption', 'difficulty' => 'hard'],
            ],
        ];
    }
    
    /**
     * Basic evaluation when AI unavailable
     */
    protected function getBasicEvaluation($answer)
    {
        $wordCount = str_word_count($answer);
        $score = min(100, ($wordCount / 100) * 100); // Basic score based on length
        
        return [
            'score' => $score,
            'strengths' => ['Answer provided'],
            'areas_for_improvement' => ['Consider adding more specific examples'],
            'suggestions' => ['Use the STAR method', 'Quantify your achievements'],
            'overall_feedback' => 'Your answer has been recorded. Consider providing more specific examples and concrete results.',
        ];
    }
    
    /**
     * Generic common questions
     */
    protected function getGenericCommonQuestions()
    {
        return [
            ['question' => 'Tell me about yourself', 'type' => 'behavioral', 'frequency' => 'very_common', 'tips' => 'Keep it professional and relevant to the role'],
            ['question' => 'Why do you want to work here?', 'type' => 'behavioral', 'frequency' => 'very_common', 'tips' => 'Research the company beforehand'],
            ['question' => 'What are your strengths and weaknesses?', 'type' => 'behavioral', 'frequency' => 'very_common', 'tips' => 'Be honest but strategic'],
            ['question' => 'Where do you see yourself in 5 years?', 'type' => 'behavioral', 'frequency' => 'common', 'tips' => 'Show ambition aligned with the role'],
            ['question' => 'Why should we hire you?', 'type' => 'behavioral', 'frequency' => 'common', 'tips' => 'Highlight unique value you bring'],
        ];
    }
    
    /**
     * Generic interview tips
     */
    public function getGenericTips()
    {
        return [
            ['tip' => 'Research the company thoroughly before the interview', 'category' => 'preparation'],
            ['tip' => 'Prepare specific examples using the STAR method', 'category' => 'preparation'],
            ['tip' => 'Dress appropriately for the company culture', 'category' => 'presentation'],
            ['tip' => 'Arrive 10-15 minutes early', 'category' => 'presentation'],
            ['tip' => 'Maintain good eye contact and positive body language', 'category' => 'presentation'],
            ['tip' => 'Ask thoughtful questions about the role and company', 'category' => 'behavioral'],
            ['tip' => 'Follow up with a thank-you email within 24 hours', 'category' => 'behavioral'],
        ];
    }
    
    /**
     * Generic negotiation guide
     */
    protected function getGenericNegotiationGuide()
    {
        return [
            'opening_statement' => 'Thank you for the offer. I\'m excited about this opportunity. Before accepting, I\'d like to discuss the compensation package.',
            'justification_points' => [
                'Your years of experience and proven track record',
                'Specialized skills that match the role requirements',
                'Market research showing competitive salaries',
            ],
            'negotiation_tactics' => [
                'Focus on value you bring, not personal needs',
                'Use market data to support your request',
                'Be prepared to walk away if needed',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // VANTAGE INTELLIGENCE LAYER
    // ─────────────────────────────────────────────────────────────

    /**
     * Build session context for the Executive steering layer.
     */
    public function buildSessionContext(InterviewSession $session): array
    {
        return [
            'role'        => $session->role_title ?? 'Professional',
            'company'     => $session->company_name ?? '',
            'focus_skill' => $session->focus_skill ?? '',
            'experience'  => $session->user?->profile?->years_experience ?? null,
        ];
    }

    /**
     * Get a turn-level Executive steering prompt for the interviewer AI persona.
     *
     * @param  InterviewSession $session
     * @param  int              $turn           1-based turn index
     * @param  array            $evidenceSoFar  Map of skill => count captured so far
     * @return string
     */
    public function getExecutivePrompt(InterviewSession $session, int $turn, array $evidenceSoFar): string
    {
        $context = $this->buildSessionContext($session);
        return $this->executive->buildSteeringPrompt($turn, $evidenceSoFar, $context);
    }

    /**
     * Get the full combined system prompt (base persona + Executive steering).
     *
     * @param  string $basePersona
     * @param  InterviewSession $session
     * @param  int    $turn
     * @param  array  $evidenceSoFar
     * @return string
     */
    public function getFullSystemPrompt(string $basePersona, InterviewSession $session, int $turn, array $evidenceSoFar): string
    {
        $context = $this->buildSessionContext($session);
        return $this->executive->buildFullSystemPrompt($basePersona, $turn, $evidenceSoFar, $context);
    }

    /**
     * Run the Vantage Evaluator after a session ends and dispatch badge check.
     * Should be called when interview_session.status transitions to 'completed'.
     *
     * @return array  The skill map
     */
    public function runVantageEvaluator(InterviewSession $session): array
    {
        try {
            $skillMap = $this->evaluator->evaluateInterviewSession($session);

            // Dispatch badge check asynchronously
            CheckAndAwardSkillBadges::dispatch(
                $session->user,
                'interview_session',
                $session->id,
                $skillMap
            );

            return $skillMap;
        } catch (\Exception $e) {
            Log::error('MockInterviewService: Vantage evaluator failed', [
                'session_id' => $session->id,
                'error'      => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Parse a raw JSON string from the LLM into a normalised skill map.
     */
    public function parseSkillMap(string $rawJson): array
    {
        return $this->evaluator->parseSkillMap($rawJson);
    }
}
