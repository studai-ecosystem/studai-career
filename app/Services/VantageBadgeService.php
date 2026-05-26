<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\VantageSkillAward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * VantageBadgeService
 *
 * Determines whether a user has earned a Vantage skill tier award
 * after a session completes. Awards are anti-gamed by requiring
 * multiple qualifying sessions for Proficient and Advanced tiers.
 */
class VantageBadgeService
{
    // Proficient + Advanced require this many sessions with a qualifying score
    private const MIN_SESSIONS_FOR_HIGH_TIER = 3;

    /**
     * Check all skills for a user and award any newly unlocked tiers.
     *
     * @param  User   $user
     * @param  string $sourceType  'interview_session'|'coaching_session'|'negotiation_session'
     * @param  int    $sourceId
     * @param  array  $skillMap    Result from VantageEvaluatorService::evaluate()
     * @return array  List of newly awarded VantageSkillAward instances
     */
    public function checkAndAward(User $user, string $sourceType, int $sourceId, array $skillMap): array
    {
        $newAwards = [];

        foreach (VantageEvaluatorService::SKILLS as $skillKey => $skillLabel) {
            $score = (float) ($skillMap[$skillKey]['score'] ?? 0.0);

            if ($score < VantageSkillAward::TIER_THRESHOLDS[VantageSkillAward::TIER_EMERGING]) {
                continue;
            }

            foreach (array_reverse(VantageSkillAward::TIER_THRESHOLDS) as $tier => $threshold) {
                if ($score < $threshold) {
                    continue;
                }

                // Already awarded this tier?
                $alreadyAwarded = VantageSkillAward::where('user_id', $user->id)
                    ->where('skill', $skillKey)
                    ->where('tier', $tier)
                    ->exists();

                if ($alreadyAwarded) {
                    break;
                }

                // High tiers require multiple qualifying sessions (anti-gaming)
                if (in_array($tier, [VantageSkillAward::TIER_PROFICIENT, VantageSkillAward::TIER_ADVANCED], true)) {
                    $qualifyingCount = $this->countQualifyingSessions($user, $skillKey, $threshold);
                    if ($qualifyingCount < self::MIN_SESSIONS_FOR_HIGH_TIER) {
                        break;
                    }
                }

                $award = VantageSkillAward::create([
                    'user_id'     => $user->id,
                    'skill'       => $skillKey,
                    'tier'        => $tier,
                    'score'       => $score,
                    'source_type' => $sourceType,
                    'source_id'   => $sourceId,
                    'unlocked_at' => now(),
                ]);

                $newAwards[] = $award;
                Log::info('Vantage skill award unlocked', [
                    'user_id' => $user->id,
                    'skill'   => $skillKey,
                    'tier'    => $tier,
                    'score'   => $score,
                ]);

                break; // Only award the highest qualifying tier at a time
            }
        }

        // Recalculate user composite vantage score
        $this->updateUserVantageScore($user);

        return $newAwards;
    }

    /**
     * Return all awards for a user grouped by skill.
     */
    public function getUserAwards(User $user): array
    {
        return VantageSkillAward::where('user_id', $user->id)
            ->orderByDesc('unlocked_at')
            ->get()
            ->groupBy('skill')
            ->map(fn ($awards) => $awards->sortByDesc('score')->first())
            ->toArray();
    }

    /**
     * Return highest awarded tier per skill for a user.
     */
    public function getUserTopTiers(User $user): array
    {
        $tierOrder = [
            VantageSkillAward::TIER_ADVANCED   => 4,
            VantageSkillAward::TIER_PROFICIENT  => 3,
            VantageSkillAward::TIER_DEVELOPING  => 2,
            VantageSkillAward::TIER_EMERGING    => 1,
        ];

        $rows = VantageSkillAward::where('user_id', $user->id)->get();
        $result = [];

        foreach ($rows as $award) {
            $existing = $result[$award->skill] ?? null;
            if (!$existing || ($tierOrder[$award->tier] ?? 0) > ($tierOrder[$existing['tier']] ?? 0)) {
                $result[$award->skill] = [
                    'tier'        => $award->tier,
                    'score'       => (float) $award->score,
                    'unlocked_at' => $award->unlocked_at,
                ];
            }
        }

        return $result;
    }

    /**
     * Count how many sessions have a qualifying score for a skill/threshold.
     * Combines interview + coaching + negotiation sessions.
     */
    private function countQualifyingSessions(User $user, string $skill, float $threshold): int
    {
        $count = 0;

        // Interview sessions
        $count += DB::table('interview_sessions')
            ->where('user_id', $user->id)
            ->whereNotNull('skill_map')
            ->get(['skill_map'])
            ->filter(function ($row) use ($skill, $threshold) {
                $map = json_decode($row->skill_map, true);
                return (float) ($map[$skill]['score'] ?? 0) >= $threshold;
            })
            ->count();

        // Coaching skill scores
        $count += DB::table('coaching_skill_scores')
            ->where('user_id', $user->id)
            ->where('skill', $skill)
            ->where('score', '>=', $threshold)
            ->count();

        // Negotiation sessions
        $count += DB::table('negotiation_sessions')
            ->where('user_id', $user->id)
            ->whereNotNull('skill_scores')
            ->get(['skill_scores'])
            ->filter(function ($row) use ($skill, $threshold) {
                $map = json_decode($row->skill_scores, true);
                return (float) ($map[$skill]['score'] ?? 0) >= $threshold;
            })
            ->count();

        return $count;
    }

    private function updateUserVantageScore(User $user): void
    {
        // Average the best score per skill across all session types
        $scores = [];

        foreach (array_keys(VantageEvaluatorService::SKILLS) as $skill) {
            $best = 0.0;

            $interviewBest = DB::table('interview_sessions')
                ->where('user_id', $user->id)
                ->whereNotNull('skill_map')
                ->get(['skill_map'])
                ->map(fn ($r) => (float) (json_decode($r->skill_map, true)[$skill]['score'] ?? 0))
                ->max();

            $coachBest = DB::table('coaching_skill_scores')
                ->where('user_id', $user->id)
                ->where('skill', $skill)
                ->max('score');

            $negoBest = DB::table('negotiation_sessions')
                ->where('user_id', $user->id)
                ->whereNotNull('skill_scores')
                ->get(['skill_scores'])
                ->map(fn ($r) => (float) (json_decode($r->skill_scores, true)[$skill]['score'] ?? 0))
                ->max();

            $best = max((float) ($interviewBest ?? 0), (float) ($coachBest ?? 0), (float) ($negoBest ?? 0));
            if ($best > 0) {
                $scores[] = $best;
            }
        }

        if (!empty($scores)) {
            $composite = round(array_sum($scores) / count($scores), 2);
            $user->updateQuietly(['vantage_score' => $composite]);
        }
    }
}
