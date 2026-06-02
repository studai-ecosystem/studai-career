<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIDecisionLog extends Model
{
    protected $table = 'ai_decision_logs';

    protected $fillable = [
        'subject_type', 'subject_id',
        'actor_id', 'actor_type',
        'decision_type', 'model_used', 'prompt_version',
        'ai_score', 'ai_recommendation', 'confidence',
        'score_factors', 'evidence', 'natural_language_explanation',
        'bias_flagged', 'bias_indicators',
        'input_context', 'raw_ai_response', 'processing_ms',
        'final_decision', 'was_overridden',
    ];

    protected $casts = [
        'ai_score'         => 'float',
        'confidence'       => 'float',
        'score_factors'    => 'array',
        'evidence'         => 'array',
        'bias_indicators'  => 'array',
        'input_context'    => 'array',
        'raw_ai_response'  => 'array',
        'bias_flagged'     => 'boolean',
        'was_overridden'   => 'boolean',
    ];

    // ── Decision types ──────────────────────────────────────────────────────────
    public const TYPE_SHORTLIST  = 'shortlist';
    public const TYPE_REJECT     = 'reject';
    public const TYPE_SCORE      = 'score';
    public const TYPE_RECOMMEND  = 'recommend';
    public const TYPE_FLAG       = 'flag';
    public const TYPE_ATS        = 'ats_score';
    public const TYPE_SCREENING  = 'screening_score';
    public const TYPE_FIT        = 'fit_score';

    // ── Recommendations ─────────────────────────────────────────────────────────
    public const REC_SHORTLIST = 'shortlist';
    public const REC_REJECT    = 'reject';
    public const REC_REVIEW    = 'review';
    public const REC_HOLD      = 'hold';

    // ── Relationships ───────────────────────────────────────────────────────────
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function override(): HasOne
    {
        return $this->hasOne(HumanOverride::class, 'ai_decision_log_id');
    }

    // ── Accessors ───────────────────────────────────────────────────────────────
    public function getScoreLabelAttribute(): string
    {
        $score = (int) ($this->ai_score ?? 0);
        return match (true) {
            $score >= 85 => 'Excellent',
            $score >= 70 => 'Strong',
            $score >= 55 => 'Good',
            $score >= 40 => 'Moderate',
            default      => 'Low',
        };
    }

    public function getScoreColorAttribute(): string
    {
        $score = (int) ($this->ai_score ?? 0);
        return match (true) {
            $score >= 85 => 'success',
            $score >= 70 => 'info',
            $score >= 55 => 'warning',
            default      => 'danger',
        };
    }

    public function getEffectiveDecisionAttribute(): string
    {
        return $this->was_overridden
            ? ($this->override?->override_decision ?? $this->final_decision ?? $this->ai_recommendation ?? '-')
            : ($this->ai_recommendation ?? '-');
    }

    // ── Scopes ──────────────────────────────────────────────────────────────────
    public function scopeBiasFlagged($query)
    {
        return $query->where('bias_flagged', true);
    }

    public function scopeOverridden($query)
    {
        return $query->where('was_overridden', true);
    }

    public function scopeForSubject($query, string $type, int $id)
    {
        return $query->where('subject_type', $type)->where('subject_id', $id);
    }
}
