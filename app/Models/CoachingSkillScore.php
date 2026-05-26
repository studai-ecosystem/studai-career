<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachingSkillScore extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'skill',
        'score',
        'sub_scores',
        'level',
        'evidence_quotes',
        'improvement_tips',
    ];

    protected $casts = [
        'score'          => 'decimal:2',
        'sub_scores'     => 'array',
        'evidence_quotes'=> 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CareerCoachSession::class, 'session_id');
    }
}
