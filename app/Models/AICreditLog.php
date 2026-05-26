<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AICreditLog extends Model
{
    protected $table = 'ai_credit_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'credits_used',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'credits_used' => 'integer',
    ];

    // Action labels for display
    public static array $actionLabels = [
        'cover_letter'   => 'AI Cover Letter',
        'resume_review'  => 'Resume Review',
        'interview_prep' => 'Interview Prep',
        'ai_apply'       => 'One-Click AI Apply',
        'skill_analysis' => 'Skill Gap Analysis',
        'career_coach'   => 'Career Coaching',
        'salary_insight' => 'Salary Insight',
        'experience_desc'=> 'Experience Description',
        'achievement'    => 'AI Achievements',
    ];

    // Action badge colors
    public static array $actionColors = [
        'cover_letter'   => ['bg' => '#ede9fe', 'text' => '#6d28d9'],
        'resume_review'  => ['bg' => '#e0f2fe', 'text' => '#0369a1'],
        'interview_prep' => ['bg' => '#fce7f3', 'text' => '#9d174d'],
        'ai_apply'       => ['bg' => '#d1fae5', 'text' => '#065f46'],
        'skill_analysis' => ['bg' => '#fef3c7', 'text' => '#92400e'],
        'career_coach'   => ['bg' => '#fee2e2', 'text' => '#991b1b'],
        'salary_insight' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
        'experience_desc'=> ['bg' => '#fdf4ff', 'text' => '#7e22ce'],
        'achievement'    => ['bg' => '#f0fdf4', 'text' => '#166534'],
    ];

    public function getActionLabelAttribute(): string
    {
        return static::$actionLabels[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    public function getActionColorAttribute(): array
    {
        return static::$actionColors[$this->action] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
