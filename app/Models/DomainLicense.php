<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainLicense extends Model
{
    protected $fillable = [
        'domain',
        'organization_name',
        'subscription_plan_id',
        'total_seats',
        'seats_used',
        'auto_assign',
        'is_active',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'total_seats' => 'integer',
        'seats_used' => 'integer',
        'auto_assign' => 'boolean',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Normalize a raw domain or email into a bare lowercase domain.
     */
    public static function normalizeDomain(string $value): string
    {
        $value = strtolower(trim($value));

        if (str_contains($value, '@')) {
            $value = substr($value, strpos($value, '@') + 1);
        }

        return ltrim($value, '@');
    }

    /**
     * Whether this license still has unused seats.
     */
    public function hasAvailableSeats(): bool
    {
        return $this->total_seats === 0 || $this->seats_used < $this->total_seats;
    }

    /**
     * Whether the license is currently usable.
     */
    public function isUsable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        return $this->hasAvailableSeats();
    }

    public function getSeatsRemainingAttribute(): int
    {
        if ($this->total_seats === 0) {
            return PHP_INT_MAX;
        }

        return max(0, $this->total_seats - $this->seats_used);
    }
}
