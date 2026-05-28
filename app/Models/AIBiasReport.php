<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIBiasReport extends Model
{
    protected $table = 'ai_bias_reports';

    protected $fillable = [
        'report_type', 'scope', 'scope_id',
        'period_start', 'period_end',
        'total_decisions_analysed',
        'group_metrics', 'disparity_ratios',
        'bias_severity', 'bias_level',
        'protected_attributes_affected', 'recommendations',
        'requires_review', 'reviewed',
        'reviewed_by', 'reviewed_at', 'review_notes',
        'status',
    ];

    protected $casts = [
        'period_start'                  => 'date',
        'period_end'                    => 'date',
        'group_metrics'                 => 'array',
        'disparity_ratios'              => 'array',
        'protected_attributes_affected' => 'array',
        'recommendations'               => 'array',
        'bias_severity'                 => 'float',
        'requires_review'               => 'boolean',
        'reviewed'                      => 'boolean',
        'reviewed_at'                   => 'datetime',
    ];

    // Bias levels
    public const LEVEL_NONE     = 'none';
    public const LEVEL_LOW      = 'low';
    public const LEVEL_MODERATE = 'moderate';
    public const LEVEL_HIGH     = 'high';
    public const LEVEL_CRITICAL = 'critical';

    // Report types
    public const TYPE_DEMOGRAPHIC   = 'demographic';
    public const TYPE_GEOGRAPHIC    = 'geographic';
    public const TYPE_INSTITUTIONAL = 'institutional';
    public const TYPE_NAME_PATTERN  = 'name_pattern';
    public const TYPE_INTERSECTIONAL = 'intersectional';

    // ── Relationships ──────────────────────────────────────────────────────────
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────
    public function getBiasLevelColorAttribute(): string
    {
        return match ($this->bias_level) {
            self::LEVEL_NONE     => 'success',
            self::LEVEL_LOW      => 'info',
            self::LEVEL_MODERATE => 'warning',
            self::LEVEL_HIGH     => 'danger',
            self::LEVEL_CRITICAL => 'danger',
            default              => 'gray',
        };
    }

    public function getSeverityPercentAttribute(): int
    {
        return (int) (($this->bias_severity ?? 0) * 100);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────
    public function scopeRequiresReview($query)
    {
        return $query->where('requires_review', true)->where('reviewed', false);
    }

    public function scopeHighSeverity($query)
    {
        return $query->whereIn('bias_level', [self::LEVEL_HIGH, self::LEVEL_CRITICAL]);
    }
}
