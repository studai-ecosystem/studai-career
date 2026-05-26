<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoundAttempt extends Model
{
    protected $fillable = [
        'hiring_round_id', 'user_id', 'application_id',
        'questions', 'answers', 'score', 'ai_feedback',
        'status', 'started_at', 'submitted_at', 'violations',
    ];

    protected $casts = [
        'questions'    => 'array',
        'answers'      => 'array',
        'score'        => 'integer',
        'violations'   => 'integer',
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function hiringRound(): BelongsTo
    {
        return $this->belongsTo(HiringRound::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
