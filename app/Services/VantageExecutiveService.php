<?php

declare(strict_types=1);

namespace App\Services;

/**
 * VantageExecutiveService
 *
 * Builds turn-by-turn adaptive steering prompts for the AI interviewer /
 * coach persona. Operates in three phases to maximise skill signal
 * captured across a session.
 *
 * Phase 1 (turns 1–3):  Rapport — open, behavioural questions
 * Phase 2 (turns 4–7):  Targeted probes — skill gaps, situational
 * Phase 3 (turns 8–10): Synthesis — meta-cognitive, reflection
 */
class VantageExecutiveService
{
    // Skills that are being tracked
    private const SKILLS = ['critical_thinking', 'collaboration', 'communication', 'creativity', 'adaptability'];

    /**
     * Build the next system prompt addendum based on current turn and evidence.
     *
     * @param  int    $turn              1-based turn counter
     * @param  array  $evidenceSoFar     Map of skill => signals already captured (bool or count)
     * @param  array  $sessionContext    ['role', 'company', 'focus_skill', 'scenario']
     * @return string
     */
    public function buildSteeringPrompt(int $turn, array $evidenceSoFar, array $sessionContext = []): string
    {
        $phase        = $this->detectPhase($turn);
        $coveredSkills = $this->getCoveredSkills($evidenceSoFar);
        $uncoveredSkills = array_diff(self::SKILLS, $coveredSkills);
        $focusSkill   = $sessionContext['focus_skill'] ?? (empty($uncoveredSkills) ? 'critical_thinking' : reset($uncoveredSkills));
        $role         = $sessionContext['role'] ?? 'professional';
        $scenario     = $sessionContext['scenario'] ?? '';

        return match ($phase) {
            1 => $this->phase1Prompt($turn, $focusSkill, $role, $uncoveredSkills),
            2 => $this->phase2Prompt($turn, $focusSkill, $role, $uncoveredSkills, $coveredSkills),
            3 => $this->phase3Prompt($turn, $coveredSkills),
        };
    }

    /**
     * Update evidence tracker after reading the user's latest response.
     * Returns updated $evidenceSoFar array.
     *
     * @param  array  $evidenceSoFar
     * @param  array  $signals   Map of skill => bool indicating signal observed
     * @return array
     */
    public function updateEvidence(array $evidenceSoFar, array $signals): array
    {
        foreach ($signals as $skill => $observed) {
            if ($observed) {
                $evidenceSoFar[$skill] = ($evidenceSoFar[$skill] ?? 0) + 1;
            }
        }
        return $evidenceSoFar;
    }

    /**
     * Return a full system prompt for the interviewer/coach persona including
     * the Executive steering layer.
     *
     * @param  string $basePersona     Base system prompt (role/company context)
     * @param  int    $turn
     * @param  array  $evidenceSoFar
     * @param  array  $sessionContext
     * @return string
     */
    public function buildFullSystemPrompt(string $basePersona, int $turn, array $evidenceSoFar, array $sessionContext = []): string
    {
        $steering = $this->buildSteeringPrompt($turn, $evidenceSoFar, $sessionContext);
        return $basePersona . "\n\n" . $steering;
    }

    private function detectPhase(int $turn): int
    {
        if ($turn <= 3) {
            return 1;
        }
        if ($turn <= 7) {
            return 2;
        }
        return 3;
    }

    private function getCoveredSkills(array $evidenceSoFar): array
    {
        return array_keys(array_filter($evidenceSoFar, fn ($count) => ($count ?? 0) >= 1));
    }

    private function phase1Prompt(int $turn, string $focusSkill, string $role, array $uncovered): string
    {
        $focusLabel   = str_replace('_', ' ', $focusSkill);
        $uncoveredStr = implode(', ', array_map(fn ($s) => str_replace('_', ' ', $s), $uncovered));

        return <<<PROMPT
[VANTAGE EXECUTIVE DIRECTIVE — PHASE 1: RAPPORT (Turn {$turn})]

Your goal in this phase is to build psychological safety while capturing baseline evidence for future-ready competencies.

- Ask ONE open behavioural question related to a past experience that naturally surfaces: {$focusLabel}
- Keep your tone warm, curious, non-evaluative. Do NOT telegraph what you are assessing.
- Do NOT ask follow-up probes yet — just the opening question.
- The candidate is applying for: {$role}
- Skills awaiting evidence: {$uncoveredStr}

Frame your question around a real workplace scenario (e.g. "Tell me about a time when…").
PROMPT;
    }

    private function phase2Prompt(int $turn, string $focusSkill, string $role, array $uncovered, array $covered): string
    {
        $focusLabel   = str_replace('_', ' ', $focusSkill);
        $coveredStr   = implode(', ', array_map(fn ($s) => str_replace('_', ' ', $s), $covered));
        $uncoveredStr = implode(', ', array_map(fn ($s) => str_replace('_', ' ', $s), $uncovered));
        $subSkills = [
            'critical_thinking' => 'probing their reasoning chain and assumptions',
            'collaboration'     => 'exploring their role in team dynamics and conflict',
            'communication'     => 'requesting a concrete example of explaining something complex',
            'creativity'        => 'asking them to reframe a problem or suggest a novel approach',
            'adaptability'      => 'presenting an unexpected constraint and asking how they would pivot',
        ];
        $probe = $subSkills[$focusSkill] ?? 'asking a deeper situational question';

        return <<<PROMPT
[VANTAGE EXECUTIVE DIRECTIVE — PHASE 2: TARGETED PROBES (Turn {$turn})]

Skills with sufficient evidence: {$coveredStr}
Skills still needing evidence: {$uncoveredStr}

PRIORITY: Probe for evidence of **{$focusLabel}** by {$probe}.

Rules:
- Build naturally on what the candidate just said — do NOT pivot abruptly.
- Use the STAR technique if appropriate (probe for Situation → Task → Action → Result).
- Ask ONE question only. Make it specific and behavioural.
- You are assessing a {$role} candidate. Keep expectations calibrated accordingly.
PROMPT;
    }

    private function phase3Prompt(int $turn, array $covered): string
    {
        $coveredStr = implode(', ', array_map(fn ($s) => str_replace('_', ' ', $s), $covered));

        return <<<PROMPT
[VANTAGE EXECUTIVE DIRECTIVE — PHASE 3: SYNTHESIS (Turn {$turn})]

Evidence has been gathered for: {$coveredStr}

In this final phase:
1. Ask ONE reflective, meta-cognitive question (e.g. "Looking back across everything we've discussed, what do you think your greatest professional strength is, and what's an area you're actively developing?")
2. Allow the candidate to self-assess — their self-awareness is itself a signal.
3. Do NOT introduce new topics. Let the conversation converge naturally.
4. Prepare to close warmly in the next turn if turn ≥ 9.
PROMPT;
    }
}
