<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MarketplaceContract;
use App\Models\User;

class MarketplaceContractPolicy
{
    public function view(User $user, MarketplaceContract $contract): bool
    {
        return $user->id === $contract->employer_id || $user->id === $contract->freelancer_id;
    }

    public function update(User $user, MarketplaceContract $contract): bool
    {
        return $user->id === $contract->employer_id || $user->id === $contract->freelancer_id;
    }

    public function cancel(User $user, MarketplaceContract $contract): bool
    {
        return $user->id === $contract->employer_id || $user->id === $contract->freelancer_id;
    }
}
