<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\InterviewSession;
use App\Models\NegotiationSession;
use App\Models\User;
use App\Services\AI\AIService;
use Illuminate\Support\Facades\Log;

/**
 * VantageEvaluatorService
 *
 * Shared post-session AI evaluator used by Interview Lab, Career Coach,
 * and Negotiation Strategist. Analyses a full conversation transcript
 * against a pedagogical rubric and returns a structured skill map.
 */
class VantageEvaluatorService extends AIService
{
    public const SKILLS = [
        'critical_thinking' => 'Critical Thinking',
        'collaboration'     => 'Collaboration',
        'communication'     => 'Communication',
        'creativity'        => 'Creativity',
        'adaptability'      => 'Adaptability',
    ];

    public const SKILL_LEVELS = [
        1 => 'Not Demonstrated',
        2 => 'Emerging',
        3 => 'Developing',
        4 => 'Proficient',
        5 => 'Advanced',
    ];

    public const SUB_SKILLS = [
        'critical_thinking' => ['Assumption identification', 'Logical reasoning', 'Evidence use', 'Conclusion quality'],
        'collaboration'     => ['Active listening', 'Shared ownership', 'Conflict navigation', 'Team orientation'],
        'communication'     => ['Clarity', 'Conciseness', 'Audience awareness', 'Persuasion'],
        'creativity'        => ['Originality', 'Lateral thinking', 'Reframing', 'Novel solutions'],
        'adaptability'      => ['Openness to change', 'Resilience under pressure', 'Learning agility', 'Pivot quality'],
    ];

    /**
     * Evaluate a full transcript and return a structured skill map.
     *
     * @param array  $transcript  Array of ['role' => 'user'|'assistant', 'content' => string]
     * @param User   $user
     * @param array  $context     Optional context (role, experience, target)
     * @return array
     */
    public function evaluate(array $transcript, User $user, array $context = []): array
    {
        $transcriptText = $this->formatTranscript($transcript);

        // Count how many user turns have actual content (non-empty answers)
        $totalTurns    = collect($transcript)->where('role', 'user')->count();
        $answeredTurns = collect($transcript)->where('role', 'user')->filter(fn($t) => strlen(trim($t['content'] ?? '')) > 10)->count();
        $context['answered_turns'] = $answeredTurns;
        $context['total_turns']    = $totalTurns;

        $prompt = $this->buildEvaluatorPrompt($transcriptText, $user, $context);

        try {
            $raw = $this->callAzureOpenAI(
                [
                    ['role' => 'system', 'content' => 'You are an expert pedagogical evaluator trained on OECD Learning Compass 2030 and WEF Future of Jobs competency frameworks. Return ONLY valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                ['temperature' => 0.3, 'max_completion_tokens' => 2000]
            );

            $skillMap = $this->parseSkillMap($raw);
            return $skillMap;
        } catch (\Exception $e) {
            Log::error('VantageEvaluator failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return $this->emptySkillMap();
        }
    }

    /**
     * Evaluate and persist results on an InterviewSession.
     */
    public function evaluateInterviewSession(InterviewSession $session): array
    {
        $transcript = $session->questions()
            ->with('responses')
            ->get()
            ->flatMap(function ($q) {
                $rows = [['role' => 'assistant', 'content' => $q->question_text]];
                foreach ($q->responses ?? [] as $r) {
                    $rows[] = ['role' => 'user', 'content' => $r->answer_text ?? ''];
                }
                return $rows;
            })
            ->toArray();

        $context = [
            'role'       => $session->role_title,
            'company'    => $session->company_name,
            'experience' => $session->user->profile?->years_experience ?? null,
        ];

        $skillMap = $this->evaluate($transcript, $session->user, $context);
        $overall  = $this->compositeScore($skillMap);

        $session->update([
            'skill_map'        => $skillMap,
            'vantage_score'    => $overall,
            'evaluator_ran_at' => now(),
        ]);

        return $skillMap;
    }

    /**
     * Evaluate and persist results on a NegotiationSession.
     */
    public function evaluateNegotiationSession(NegotiationSession $session): array
    {
        $messages = $session->messages()
            ->orderBy('created_at')
            ->get(['sender_type', 'content'])
            ->map(fn ($m) => ['role' => $m->sender_type === 'user' ? 'user' : 'assistant', 'content' => $m->content])
            ->toArray();

        $context = [
            'role'    => 'Salary Negotiation',
            'outcome' => $session->outcome ?? 'unknown',
        ];

        $skillMap = $this->evaluate($messages, $session->user, $context);

        $session->update(['skill_scores' => $skillMap]);

        return $skillMap;
    }

    /**
     * Parse raw LLM JSON output into a normalised skill map array.
     */
    public function parseSkillMap(string $raw): array
    {
        // Strip markdown code fences
        $raw = preg_replace('/```(?:json)?\s*/i', '', $raw);
        $raw = preg_replace('/```/', '', $raw);
        $raw = trim($raw);

        $data = json_decode($raw, true);

        if (!is_array($data) || !isset($data['skills'])) {
            return $this->emptySkillMap();
        }

        $map = [];
        foreach ($data['skills'] as $skill => $detail) {
            $normalKey = strtolower(str_replace(' ', '_', $skill));
            if (!array_key_exists($normalKey, self::SKILLS)) {
                continue;
            }
            $score = (float) ($detail['score'] ?? 0.0);
            $map[$normalKey] = [
                'score'       => round(min(5.0, max(0.0, $score)), 2),
                'level'       => $this->scoreToLevel($score),
                'sub_scores'  => $detail['sub_scores'] ?? [],
                'evidence'    => $detail['evidence'] ?? '',
                'improvement' => $detail['improvement'] ?? [],
            ];
        }

        $map['standout_moment'] = $data['standout_moment'] ?? '';
        $map['growth_focus']    = $data['growth_focus'] ?? '';
        $map['overall']         = $this->compositeScore($map);

        return $map;
    }

    public function compositeScore(array $skillMap): float
    {
        $scores = [];
        foreach (array_keys(self::SKILLS) as $key) {
            if (isset($skillMap[$key]['score'])) {
                $scores[] = (float) $skillMap[$key]['score'];
            }
        }
        if (empty($scores)) {
            return 0.0;
        }
        return round(array_sum($scores) / count($scores), 2);
    }

    public function scoreToLevel(float $score): string
    {
        $level = (int) round($score);
        return self::SKILL_LEVELS[max(1, min(5, $level))] ?? 'Not Demonstrated';
    }

    private function formatTranscript(array $transcript): string
    {
        return collect($transcript)
            ->map(fn ($t) => strtoupper($t['role'] ?? 'USER') . ': ' . ($t['content'] ?? ''))
            ->implode("\n\n");
    }

    private function buildEvaluatorPrompt(string $transcript, User $user, array $context): string
    {
        $role           = $context['role']           ?? 'Professional';
        $experience     = $context['experience']     ?? 'unknown';
        $answeredTurns  = $context['answered_turns'] ?? '?';
        $totalTurns     = $context['total_turns']    ?? '?';

        return <<<PROMPT
You are evaluating a workplace simulation transcript for future-ready skill competencies.

CANDIDATE CONTEXT:
- Role: {$role}
- Years of experience: {$experience}
- Questions attempted: {$answeredTurns} out of {$totalTurns}

STRICT SCORING RULES — follow these exactly:
- Score range: 0.0–5.0
- 0.0 = Skill not demonstrated at all (no evidence in transcript)
- 1.0 = Emerging (very weak evidence)
- 2.0 = Developing
- 3.0 = Proficient
- 4.0 = Advanced
- 5.0 = Expert
- NEVER give a score above 0.0 for a skill that has NO direct evidence in the transcript.
- If the candidate skipped or did not answer questions that would reveal a skill, score that skill 0.0.
- Be strict and fair: a short or vague answer is NOT evidence of a skill.

TRANSCRIPT:
{$transcript}

Evaluate the candidate across these 5 future-ready skills:
1. critical_thinking
2. collaboration
3. communication
4. creativity
5. adaptability

For each skill return:
- score: float 1.0–5.0 (1=Not Demonstrated, 2=Emerging, 3=Developing, 4=Proficient, 5=Advanced)
- sub_scores: object with sub-skill labels as keys and scores 1-5 as values
- evidence: one direct quote or paraphrase from the transcript that justifies the score
- improvement: array of 1-3 specific, actionable tips

Also return:
- standout_moment: one sentence describing the candidate's most impressive moment
- growth_focus: the single most important skill area to develop

Return ONLY this JSON structure:
{
  "skills": {
    "critical_thinking": { "score": 3.5, "sub_scores": { "Assumption identification": 3, "Logical reasoning": 4, "Evidence use": 3, "Conclusion quality": 4 }, "evidence": "...", "improvement": ["...", "..."] },
    "collaboration":     { "score": 4.0, "sub_scores": { ... }, "evidence": "...", "improvement": ["..."] },
    "communication":     { "score": 3.0, "sub_scores": { ... }, "evidence": "...", "improvement": ["..."] },
    "creativity":        { "score": 2.5, "sub_scores": { ... }, "evidence": "...", "improvement": ["..."] },
    "adaptability":      { "score": 4.5, "sub_scores": { ... }, "evidence": "...", "improvement": ["..."] }
  },
  "standout_moment": "...",
  "growth_focus": "communication"
}
PROMPT;
    }

    private function emptySkillMap(): array
    {
        $map = [];
        foreach (array_keys(self::SKILLS) as $key) {
            $map[$key] = [
                'score'       => 0.0,
                'level'       => 'Not Demonstrated',
                'sub_scores'  => [],
                'evidence'    => '',
                'improvement' => [],
            ];
        }
        $map['standout_moment'] = '';
        $map['growth_focus']    = '';
        $map['overall']         = 0.0;
        return $map;
    }
}
