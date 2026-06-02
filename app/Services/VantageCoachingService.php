<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CareerCoachSession;
use App\Models\CoachingSkillScore;
use App\Models\User;
use App\Services\AI\AIService;
use Illuminate\Support\Facades\Log;

/**
 * VantageCoachingService
 *
 * Manages the Skills Practice coaching session type.
 * Provides 5 workplace scenario templates and applies Executive steering.
 */
class VantageCoachingService extends AIService
{
    public const SCENARIOS = [
        'collaboration' => [
            'id'          => 'collaboration',
            'label'       => 'Team Conflict Resolution',
            'skill'       => 'collaboration',
            'description' => 'Navigate a disagreement between two teammates with opposing views on a project direction.',
            'context'     => 'You are a mid-level professional. Two teammates, Alex and Sam, disagree on the product roadmap. Alex wants to focus on speed; Sam insists on quality. You need to facilitate a resolution before the 3pm board update.',
        ],
        'critical_thinking' => [
            'id'          => 'critical_thinking',
            'label'       => 'Data-Driven Decision Making',
            'skill'       => 'critical_thinking',
            'description' => 'Evaluate conflicting data sets and recommend a course of action to your manager.',
            'context'     => 'Marketing data shows a 40% uplift in engagement from Campaign A, but conversion is flat. Finance wants to cut Campaign A. Sales has anecdotal evidence it builds pipeline. You have 10 minutes to present your recommendation.',
        ],
        'communication' => [
            'id'          => 'communication',
            'label'       => 'Stakeholder Alignment',
            'skill'       => 'communication',
            'description' => 'Communicate a project delay to senior stakeholders without damaging trust.',
            'context'     => 'The product launch will be delayed by 3 weeks due to a third-party API issue. The CEO and two enterprise clients are expecting the delivery on Friday. You must inform them today.',
        ],
        'adaptability' => [
            'id'          => 'adaptability',
            'label'       => 'Pivoting Under Pressure',
            'skill'       => 'adaptability',
            'description' => 'Respond to an unexpected strategic shift that invalidates two months of work.',
            'context'     => 'The company has just announced a pivot from B2C to B2B. Your team\'s roadmap — two months of work — is now deprioritised. You have a team standup in 30 minutes and need to reset priorities.',
        ],
        'creativity' => [
            'id'          => 'creativity',
            'label'       => 'Innovation Sprint',
            'skill'       => 'creativity',
            'description' => 'Generate novel solutions to a customer retention problem with limited resources.',
            'context'     => 'Monthly churn has increased from 3% to 9% over the last quarter. The budget for initiatives is $0. You have a 30-minute brainstorm with two junior team members. The CEO wants 3 actionable ideas by end of day.',
        ],
    ];

    private VantageExecutiveService $executive;
    private VantageEvaluatorService $evaluator;

    public function __construct(
        VantageExecutiveService $executive,
        VantageEvaluatorService $evaluator,
    ) {
        parent::__construct();
        $this->executive = $executive;
        $this->evaluator = $evaluator;
    }

    /**
     * Return all scenario templates for the UI to display.
     */
    public function getScenarios(): array
    {
        return self::SCENARIOS;
    }

    /**
     * Build the base system prompt for a Skills Practice session.
     */
    public function buildBasePersona(string $scenarioId, User $user): string
    {
        $scenario = self::SCENARIOS[$scenarioId] ?? self::SCENARIOS['collaboration'];
        $userName = $user->name ?? 'the candidate';

        return <<<PERSONA
You are a world-class executive coach running a Skills Practice session for {$userName}.

SESSION SCENARIO:
{$scenario['description']}

FULL CONTEXT:
{$scenario['context']}

Your role:
- You play the OTHER characters in this scenario (colleagues, managers, clients) — not a coach observer.
- Stay fully in character. Respond as the characters would in real life.
- Be challenging but fair. Create genuine cognitive load through realistic workplace tensions.
- After the user responds, briefly step out of character to offer ONE crisp coaching observation (in [COACH] tags), then continue the scenario.

Start the scenario immediately with the opening situation. Stay in character as the scenario's human characters for immersion. This is an AI-driven simulation: never claim to be a real human, and if the user directly asks whether you are an AI, answer honestly before resuming the scenario.
PERSONA;
    }

    /**
     * Get the full system prompt for a given turn, including Executive steering.
     */
    public function getSystemPromptForTurn(
        string $scenarioId,
        User $user,
        int $turn,
        array $evidenceSoFar
    ): string {
        $base = $this->buildBasePersona($scenarioId, $user);
        $sessionContext = [
            'role'        => $user->profile?->current_role ?? 'Professional',
            'scenario'    => $scenarioId,
            'focus_skill' => self::SCENARIOS[$scenarioId]['skill'] ?? 'collaboration',
        ];
        return $this->executive->buildFullSystemPrompt($base, $turn, $evidenceSoFar, $sessionContext);
    }

    /**
     * Persist skill scores from a completed Skills Practice session.
     */
    public function persistSkillScores(CareerCoachSession $session, array $skillMap): void
    {
        foreach (VantageEvaluatorService::SKILLS as $skillKey => $skillLabel) {
            if (!isset($skillMap[$skillKey])) {
                continue;
            }
            $data = $skillMap[$skillKey];
            CoachingSkillScore::create([
                'user_id'          => $session->user_id,
                'session_id'       => $session->id,
                'skill'            => $skillKey,
                'score'            => $data['score'] ?? 0.0,
                'sub_scores'       => $data['sub_scores'] ?? [],
                'level'            => $data['level'] ?? 'Not Demonstrated',
                'evidence_quotes'  => $data['evidence'] ? [$data['evidence']] : [],
                'improvement_tips' => is_array($data['improvement'] ?? null) ? implode("\n", $data['improvement']) : ($data['improvement'] ?? ''),
            ]);
        }
    }

    /**
     * Return the last N skill score snapshots for a user (for trending).
     */
    public function getUserSkillTrend(User $user, string $skill, int $limit = 10): array
    {
        return CoachingSkillScore::where('user_id', $user->id)
            ->where('skill', $skill)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['score', 'level', 'created_at'])
            ->toArray();
    }
}
