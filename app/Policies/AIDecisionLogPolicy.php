<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AIDecisionLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * F16: AI decision logs contain per-round S.C.O.U.T. reasoning, raw model
 * responses, and bias indicators. This is platform-internal audit data and
 * MUST NOT be exposed to employers. Employers only ever see a candidate's
 * rank position and composite score through their own dashboards.
 *
 * Only platform staff (admin / super_admin) may read, override, or manage
 * these records. Records are append-only audit entries, so create/delete are
 * disallowed entirely (the platform writes them via ExplainableAIService).
 */
class AIDecisionLogPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Hard deny employers — they must never reach reasoning/bias data.
        if ($user->isEmployer()) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, AIDecisionLog $log): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AIDecisionLog $log): bool
    {
        // Human override / annotation is a platform-admin action.
        return $user->isAdmin();
    }

    public function delete(User $user, AIDecisionLog $log): bool
    {
        return false;
    }

    public function restore(User $user, AIDecisionLog $log): bool
    {
        return false;
    }

    public function forceDelete(User $user, AIDecisionLog $log): bool
    {
        return false;
    }
}
