<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HireOutcome — ground-truth record of a terminal employer decision plus the
 * candidate-side composite scores that informed it. Consumed by candidate
 * learning paths and (offline) S.C.O.U.T. threshold calibration.
 */
class HireOutcome extends Model
{
    protected $fillable = [
        'application_id',
        'user_id',
        'job_id',
        'company_id',
        'outcome',
        'evaluation_score',
        'skill_match_score',
        'resume_quality_score',
        'behavioural_fit_score',
        'final_rank_score',
        'decided_at',
    ];

    protected $casts = [
        'evaluation_score'      => 'decimal:2',
        'skill_match_score'     => 'decimal:2',
        'resume_quality_score'  => 'decimal:2',
        'behavioural_fit_score' => 'decimal:2',
        'final_rank_score'      => 'decimal:2',
        'decided_at'            => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
