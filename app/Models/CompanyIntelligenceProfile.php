<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyIntelligenceProfile extends Model
{
    protected $fillable = [
        'company_id',
        'onboarding_conversation',
        'industry',
        'company_size',
        'headcount',
        'founded_year',
        'cin_gst',
        'website',
        'work_culture',
        'work_mode_preference',
        'top_performer_traits',
        'salary_bands',
        'compensation_philosophy',
        'pain_points',
        'preferred_candidate_communication',
        'hiring_frequency',
        'compliance_requirements',
        'onboarding_complete',
        'completeness_score',
        'last_enriched_at',
    ];

    protected $casts = [
        'onboarding_conversation'           => 'array',
        'top_performer_traits'              => 'array',
        'salary_bands'                      => 'array',
        'pain_points'                       => 'array',
        'compliance_requirements'           => 'array',
        'onboarding_complete'               => 'boolean',
        'completeness_score'                => 'integer',
        'headcount'                         => 'integer',
        'last_enriched_at'                  => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Build a compact context string for Orin™ AI prompts.
     */
    public function toOrinContext(): string
    {
        $parts = [];
        if ($this->industry)             $parts[] = "Industry: {$this->industry}";
        if ($this->company_size)         $parts[] = "Size: {$this->company_size} ({$this->headcount} people)";
        if ($this->work_culture)         $parts[] = "Culture: {$this->work_culture}";
        if ($this->work_mode_preference) $parts[] = "Work mode: {$this->work_mode_preference}";
        if ($this->compensation_philosophy) $parts[] = "Compensation: {$this->compensation_philosophy}";
        if ($this->top_performer_traits) $parts[] = "Top performer traits: " . implode(', ', $this->top_performer_traits);
        return implode('. ', $parts);
    }
}
