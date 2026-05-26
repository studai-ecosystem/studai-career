<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HiringTest extends Model
{
    protected $fillable = [
        'job_id', 'stage', 'title', 'instructions',
        'questions', 'pass_score', 'time_limit_minutes', 'is_active',
    ];

    protected $casts = [
        'questions'           => 'array',
        'pass_score'          => 'integer',
        'time_limit_minutes'  => 'integer',
        'is_active'           => 'boolean',
    ];

    public const STAGE_LABELS = [
        'company_info_test' => 'Company Info Test',
        'aptitude'          => 'Aptitude Assessment',
        'tech_test'         => 'Technical Test',
        'non_tech_test'     => 'Non-Technical Test',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(HiringTestAttempt::class);
    }
}
