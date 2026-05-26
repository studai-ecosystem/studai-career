<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationAnswer extends Model
{
    protected $fillable = [
        'evaluation_session_id', 'question_id', 'question_index',
        'answer_text', 'answer_options', 'video_response_url',
        'score_awarded', 'max_score', 'ai_feedback', 'is_correct',
        'is_auto_scored', 'time_taken_seconds', 'time_anomaly', 'answered_at',
    ];

    protected $casts = [
        'answer_options' => 'array',
        'is_correct'     => 'boolean',
        'is_auto_scored' => 'boolean',
        'time_anomaly'   => 'boolean',
        'score_awarded'  => 'decimal:2',
        'max_score'      => 'decimal:2',
        'answered_at'    => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(EvaluationSession::class, 'evaluation_session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }
}
