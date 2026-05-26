<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationSession extends Model
{
    protected $fillable = [
        'application_id', 'job_id', 'user_id', 'status', 'session_token',
        'redis_key', 'assigned_question_ids', 'current_question_index',
        'total_questions', 'current_difficulty', 'consecutive_correct',
        'consecutive_incorrect', 'tab_switch_count', 'focus_loss_count',
        'time_anomalies', 'flagged_for_review', 'started_at', 'completed_at',
        'expires_at', 'total_time_seconds', 'raw_score', 'weighted_score', 'section_scores',
    ];

    protected $casts = [
        'assigned_question_ids' => 'array',
        'time_anomalies'        => 'array',
        'section_scores'        => 'array',
        'flagged_for_review'    => 'boolean',
        'started_at'            => 'datetime',
        'completed_at'          => 'datetime',
        'expires_at'            => 'datetime',
        'raw_score'             => 'decimal:2',
        'weighted_score'        => 'decimal:2',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(EvaluationAnswer::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isComplete(): bool
    {
        return $this->status === 'completed';
    }
}
