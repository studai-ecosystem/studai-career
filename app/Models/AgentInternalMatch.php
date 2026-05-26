<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentInternalMatch extends Model
{
    protected $fillable = [
        'user_id',
        'job_id',
        'match_score',
        'score_breakdown',
        'ai_reasoning',
        'cover_letter',
        'status',
        'application_id',
        'applied_at',
    ];

    protected $casts = [
        'match_score'     => 'integer',
        'score_breakdown' => 'array',
        'applied_at'      => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getScoreColorAttribute(): string
    {
        return match (true) {
            $this->match_score >= 80 => 'green',
            $this->match_score >= 60 => 'blue',
            $this->match_score >= 40 => 'amber',
            default                  => 'red',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Awaiting Review',
            'approved' => 'Approved',
            'skipped'  => 'Skipped',
            'applied'  => 'Applied',
            default    => ucfirst($this->status),
        };
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }
}
