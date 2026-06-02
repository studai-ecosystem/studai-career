<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\AnalyzeSkillGapsJob;
use App\Models\Profile;
use App\Services\AI\SkillValidatorService;

/**
 * M-C4/D11: Keeps AI skill analysis in sync with the user's profile.
 *
 * Skill validations are cached for 30 days keyed by user id. When a user
 * updates the source data those validations are derived from (experience,
 * education, projects, certifications, achievements or the claimed skills),
 * the cache must be invalidated and a fresh gap analysis re-triggered so the
 * candidate is never shown stale results against an updated work history.
 */
class ProfileObserver
{
    /**
     * Profile attributes that feed skill validation / gap analysis.
     *
     * @var array<int, string>
     */
    private const SKILL_SOURCE_ATTRIBUTES = [
        'skills',
        'experience',
        'education',
        'projects',
        'certifications',
    ];

    public function saved(Profile $profile): void
    {
        if (! $this->skillSourcesChanged($profile)) {
            return;
        }

        $userId = (int) $profile->user_id;
        if ($userId <= 0) {
            return;
        }

        SkillValidatorService::forgetCache($userId);

        if ($profile->relationLoaded('user') && $profile->user !== null) {
            AnalyzeSkillGapsJob::dispatch($profile->user)->afterCommit();

            return;
        }

        $user = $profile->user()->first();
        if ($user !== null) {
            AnalyzeSkillGapsJob::dispatch($user)->afterCommit();
        }
    }

    public function deleted(Profile $profile): void
    {
        $userId = (int) $profile->user_id;
        if ($userId > 0) {
            SkillValidatorService::forgetCache($userId);
        }
    }

    private function skillSourcesChanged(Profile $profile): bool
    {
        if ($profile->wasRecentlyCreated) {
            return true;
        }

        foreach (self::SKILL_SOURCE_ATTRIBUTES as $attribute) {
            if ($profile->wasChanged($attribute)) {
                return true;
            }
        }

        return false;
    }
}
