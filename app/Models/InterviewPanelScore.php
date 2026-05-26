<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewPanelScore extends Model
{
    protected $table = 'interview_panel_scores';

    protected $fillable = [
        'interview_id',
        'user_id',
        'question_key',
        'score',
        'comment',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
