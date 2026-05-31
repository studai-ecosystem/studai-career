<?php

namespace App\Observers;

use App\Models\User;
use App\Services\CacheService;
use App\Services\DomainLicenseService;

class UserObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Auto-apply a matching domain license (e.g. studai.one → preset plan/seat).
        try {
            app(DomainLicenseService::class)->applyForUser($user);
        } catch (\Throwable $e) {
            \Log::warning('Domain license auto-apply failed on user create', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Clear user-related caches
        $this->cacheService->clearUserCaches($user->id);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Clear user-related caches
        $this->cacheService->clearUserCaches($user->id);
    }
}
