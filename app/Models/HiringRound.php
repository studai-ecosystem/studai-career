<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HiringRound extends Model
{
    protected $fillable = [
        'job_id', 'name', 'type', 'round_order', 'description',
        'ai_evaluation_criteria', 'test_date', 'evaluation_days',
        'evaluation_date', 'status',
    ];

    protected $casts = [
        'test_date'       => 'date',
        'evaluation_date' => 'date',
        'evaluation_days' => 'integer',
        'round_order'     => 'integer',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(RoundAttempt::class);
    }
}
