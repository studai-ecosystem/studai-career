<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HumanOverride extends Model
{
    protected $fillable = [
        'ai_decision_log_id',
        'subject_type', 'subject_id',
        'overrider_id', 'overrider_role',
        'original_decision', 'original_score',
        'override_decision', 'override_score',
        'reason', 'override_category',
        'is_bias_correction', 'additional_context',
        'requires_justification', 'justification',
        'acknowledged_at',
    ];

    protected $casts = [
        'original_score'       => 'float',
        'override_score'       => 'float',
        'additional_context'   => 'array',
        'is_bias_correction'   => 'boolean',
        'requires_justification' => 'boolean',
        'acknowledged_at'      => 'datetime',
    ];

    // Override categories
    public const CAT_BIAS_CORRECTION   = 'bias_correction';
    public const CAT_ADDITIONAL_CONTEXT = 'additional_context';
    public const CAT_POLICY            = 'policy';
    public const CAT_ERROR             = 'error_correction';
    public const CAT_GENERAL           = 'general';

    public static function categories(): array
    {
        return [
            self::CAT_BIAS_CORRECTION    => 'Bias Correction',
            self::CAT_ADDITIONAL_CONTEXT => 'Additional Context',
            self::CAT_POLICY             => 'Policy / Compliance',
            self::CAT_ERROR              => 'Error Correction',
            self::CAT_GENERAL            => 'General Override',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────
    public function aiDecisionLog(): BelongsTo
    {
        return $this->belongsTo(AIDecisionLog::class, 'ai_decision_log_id');
    }

    public function overrider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'overrider_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Accessors ──────────────────────────────────────────────────────────────
    public function getScoreDeltaAttribute(): float
    {
        if ($this->original_score !== null && $this->override_score !== null) {
            return $this->override_score - $this->original_score;
        }
        return 0.0;
    }

    public function getCategoryLabelAttribute(): string
    {
        return static::categories()[$this->override_category] ?? ucfirst((string) $this->override_category);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────
    public function scopeBiasCorrections($query)
    {
        return $query->where('is_bias_correction', true);
    }

    public function scopeForSubject($query, string $type, int $id)
    {
        return $query->where('subject_type', $type)->where('subject_id', $id);
    }
}
