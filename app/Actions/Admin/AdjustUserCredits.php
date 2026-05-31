<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\AICreditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdjustUserCredits
{
    /**
     * Grant or deduct AI credits for a user and record an audit log entry.
     *
     * @param  'grant'|'deduct'  $direction
     */
    public function handle(User $user, int $amount, string $direction = 'grant', ?string $reason = null): void
    {
        $amount = abs($amount);

        if ($amount === 0) {
            return;
        }

        $subscription = $user->subscription;

        if ($subscription === null) {
            // Nothing to attach credits to yet; bonus is stored on the subscription.
            // Caller should assign a plan first.
            return;
        }

        if ($direction === 'grant') {
            $subscription->increment('bonus_ai_credits', $amount);
        } else {
            // Deducting reduces available balance by consuming usage headroom.
            $subscription->increment('ai_credits_used_this_month', $amount);
        }

        $subscription->forceFill(['is_admin_managed' => true])->save();

        $adminId = Auth::id();
        $description = $reason
            ?? sprintf('%s %d AI credits by admin', ucfirst($direction), $amount);

        AICreditLog::create([
            'user_id'      => $user->id,
            'action'       => $direction === 'grant' ? 'admin_grant' : 'admin_deduct',
            'description'  => $description,
            'credits_used' => $direction === 'grant' ? -$amount : $amount,
            'meta'         => [
                'admin_id'  => $adminId,
                'direction' => $direction,
            ],
        ]);
    }
}
