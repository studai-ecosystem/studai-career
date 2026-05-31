<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Admin\AssignSubscriptionPlan;
use App\Models\DomainLicense;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DomainLicenseService
{
    public function __construct(
        private readonly AssignSubscriptionPlan $assignSubscriptionPlan,
    ) {
    }

    /**
     * Find the active, usable domain license matching a user's email domain.
     */
    public function findLicenseForEmail(string $email): ?DomainLicense
    {
        $domain = DomainLicense::normalizeDomain($email);

        if ($domain === '') {
            return null;
        }

        $license = DomainLicense::query()
            ->where('domain', $domain)
            ->where('is_active', true)
            ->first();

        if ($license === null || ! $license->isUsable()) {
            return null;
        }

        return $license;
    }

    /**
     * Automatically apply a matching domain license to a newly registered user.
     * Returns true when a license seat was consumed and a plan assigned.
     */
    public function applyForUser(User $user): bool
    {
        if (empty($user->email)) {
            return false;
        }

        $license = $this->findLicenseForEmail($user->email);

        if ($license === null || ! $license->auto_assign) {
            return false;
        }

        $plan = $license->subscriptionPlan;

        if ($plan === null) {
            return false;
        }

        try {
            $this->assignSubscriptionPlan->handle(
                user: $user,
                plan: $plan,
                adminManaged: true,
                notes: 'Auto-assigned via domain license: ' . $license->domain,
            );

            $license->increment('seats_used');

            return true;
        } catch (\Throwable $e) {
            Log::warning('Domain license auto-assign failed', [
                'user_id' => $user->id,
                'domain'  => $license->domain,
                'error'   => $e->getMessage(),
            ]);

            return false;
        }
    }
}
