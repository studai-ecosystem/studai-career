<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Carbon;

class AssignSubscriptionPlan
{
    /**
     * Assign or override a user's subscription plan. Supports custom per-user
     * billing periods and admin-managed (complimentary) subscriptions.
     */
    public function handle(
        User $user,
        SubscriptionPlan $plan,
        ?Carbon $periodEnd = null,
        bool $adminManaged = true,
        ?string $notes = null
    ): UserSubscription {
        $periodStart = now();
        $periodEnd ??= $this->resolvePeriodEnd($plan, $periodStart);

        $subscription = UserSubscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'subscription_plan_id'  => $plan->id,
                'status'                => 'active',
                'current_period_start'  => $periodStart,
                'current_period_end'    => $periodEnd,
                'is_admin_managed'      => $adminManaged,
                'admin_notes'           => $notes,
                'canceled_at'           => null,
            ]
        );

        return $subscription;
    }

    private function resolvePeriodEnd(SubscriptionPlan $plan, Carbon $start): Carbon
    {
        return match ($plan->billing_period) {
            'yearly'    => $start->copy()->addYear(),
            'quarterly' => $start->copy()->addMonths(3),
            'lifetime'  => $start->copy()->addYears(100),
            default     => $start->copy()->addMonth(),
        };
    }
}
