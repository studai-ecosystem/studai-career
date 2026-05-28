<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIDisclaimer extends Model
{
    protected $fillable = [
        'key', 'title', 'body', 'context',
        'severity', 'requires_acknowledgment',
        'show_to_candidate', 'show_to_employer', 'show_to_admin',
        'is_active', 'display_order', 'metadata',
    ];

    protected $casts = [
        'requires_acknowledgment' => 'boolean',
        'show_to_candidate'       => 'boolean',
        'show_to_employer'        => 'boolean',
        'show_to_admin'           => 'boolean',
        'is_active'               => 'boolean',
        'metadata'                => 'array',
    ];

    // Contexts
    public const CTX_EMPLOYER_SCREENING = 'employer_screening';
    public const CTX_CANDIDATE_RESULT   = 'candidate_result';
    public const CTX_ADMIN              = 'admin';
    public const CTX_GLOBAL             = 'global';

    // Severities
    public const SEV_INFO     = 'info';
    public const SEV_WARNING  = 'warning';
    public const SEV_CRITICAL = 'critical';

    // ── Relationships ──────────────────────────────────────────────────────────
    public function acknowledgments(): HasMany
    {
        return $this->hasMany(AIDisclaimerAcknowledgment::class, 'disclaimer_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForContext($query, string $context)
    {
        return $query->where('context', $context)->orWhere('context', self::CTX_GLOBAL);
    }

    public function scopeForEmployer($query)
    {
        return $query->where('show_to_employer', true)->active()->ordered();
    }

    public function scopeForCandidate($query)
    {
        return $query->where('show_to_candidate', true)->active()->ordered();
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            self::SEV_WARNING  => 'warning',
            self::SEV_CRITICAL => 'danger',
            default            => 'info',
        };
    }

    public function hasBeenAcknowledgedBy(int $userId, ?string $subjectType = null, ?int $subjectId = null): bool
    {
        $q = $this->acknowledgments()->where('user_id', $userId);
        if ($subjectType && $subjectId) {
            $q->where('subject_type', $subjectType)->where('subject_id', $subjectId);
        }
        return $q->exists();
    }

    /**
     * Get all active disclaimers for a given context and role.
     */
    public static function getForRole(string $context, string $role): \Illuminate\Database\Eloquent\Collection
    {
        $roleColumn = match ($role) {
            'employer', 'recruiter' => 'show_to_employer',
            'admin'                 => 'show_to_admin',
            default                 => 'show_to_candidate',
        };

        return static::active()
            ->where(fn ($q) => $q->where('context', $context)->orWhere('context', self::CTX_GLOBAL))
            ->where($roleColumn, true)
            ->ordered()
            ->get();
    }
}
