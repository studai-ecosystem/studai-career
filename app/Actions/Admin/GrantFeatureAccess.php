<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\FeatureFlag;
use App\Models\User;

class GrantFeatureAccess
{
    /**
     * Grant or revoke per-user access to a feature flag.
     *
     * @param  'grant'|'revoke'  $direction
     */
    public function handle(User $user, FeatureFlag $flag, string $direction = 'grant'): void
    {
        $userIds = is_array($flag->user_ids) ? $flag->user_ids : [];

        if ($direction === 'grant') {
            $userIds[] = $user->id;
            $userIds = array_values(array_unique($userIds));

            // Granting to a specific user implies the flag must be enabled.
            $flag->enabled = true;
        } else {
            $userIds = array_values(array_filter(
                $userIds,
                static fn ($id): bool => (int) $id !== (int) $user->id
            ));
        }

        $flag->user_ids = $userIds;
        $flag->save();
    }
}
