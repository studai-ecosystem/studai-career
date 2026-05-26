<?php

namespace App\Policies;

use App\Models\MarketplaceProject;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MarketplaceProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MarketplaceProject $marketplaceProject): bool
    {
        return $marketplaceProject->employer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MarketplaceProject $marketplaceProject): bool
    {
        return $marketplaceProject->employer_id === $user->id;
    }

    public function delete(User $user, MarketplaceProject $marketplaceProject): bool
    {
        return $marketplaceProject->employer_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MarketplaceProject $marketplaceProject): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MarketplaceProject $marketplaceProject): bool
    {
        return false;
    }
}
