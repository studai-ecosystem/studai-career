<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBank extends Model
{
    protected $fillable = [
        'job_id', 'difficulty', 'question_type', 'topic',
        'question_text', 'options', 'correct_answer', 'evaluation_rubric',
        'time_limit_seconds', 'max_score', 'is_behavioural', 'is_culture_fit', 'is_active',
    ];

    protected $casts = [
        'options'        => 'array',
        'is_behavioural' => 'boolean',
        'is_culture_fit' => 'boolean',
        'is_active'      => 'boolean',
        'max_score'      => 'integer',
        'time_limit_seconds' => 'integer',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(EvaluationAnswer::class, 'question_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }
}
