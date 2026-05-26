<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'job_id',
        'application_number',
        'cover_letter',
        'resume_file',
        'answers',
        'status',
        'status_updated_at',
        'rejection_reason',
        'ai_reason',
        'status_history',
        'match_analysis',
        'timeline',
        'notes',
        'submitted_at',
        'viewed_at',
        'is_archived',
        'source',
        // Orin™ evaluation fields
        'evaluation_status',
        'evaluation_score',
        'skill_match_score',
        'resume_quality_score',
        'behavioural_fit_score',
        'final_rank_score',
        'rank_position',
        'evaluation_started_at',
        'evaluation_completed_at',
        'application_email_sent',
        'evaluation_invite_sent',
        'result_email_sent',
        'result_notified_at',
        'portfolio_url',
        'github_url',
        'work_sample_url',
        'screening_answers',
        'access_token',
        'is_guest_applicant',
        'guest_name',
        'guest_email',
        'guest_phone',
        // Hiring pipeline
        'hiring_stage',
        'pipeline_stage_date',
        'pipeline_stage_notes',
        'confirmation_email_sent',
        'test_link_token',
    ];

    protected $casts = [
        'answers' => 'array',
        'match_analysis' => 'array',
        'status_history' => 'array',
        'timeline' => 'array',
        'submitted_at' => 'datetime',
        'viewed_at' => 'datetime',
        'status_updated_at' => 'datetime',
        'is_archived' => 'boolean',
        // Orin™ evaluation casts
        'evaluation_score'          => 'decimal:2',
        'skill_match_score'         => 'decimal:2',
        'resume_quality_score'      => 'decimal:2',
        'behavioural_fit_score'     => 'decimal:2',
        'final_rank_score'          => 'decimal:2',
        'rank_position'             => 'integer',
        'evaluation_started_at'     => 'datetime',
        'evaluation_completed_at'   => 'datetime',
        'result_notified_at'        => 'datetime',
        'application_email_sent'    => 'boolean',
        'evaluation_invite_sent'    => 'boolean',
        'result_email_sent'         => 'boolean',
        'is_guest_applicant'        => 'boolean',
        'screening_answers'         => 'array',
        'confirmation_email_sent'   => 'boolean',
        'pipeline_stage_date'       => 'date',
    ];

    /**
     * Application status options
     */
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_VIEWED = 'viewed';
    const STATUS_SHORTLISTED = 'shortlisted';
    const STATUS_INTERVIEW_SCHEDULED = 'interview_scheduled';
    const STATUS_INTERVIEW_COMPLETED = 'interview_completed';
    const STATUS_OFFER_EXTENDED = 'offer_extended';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_WITHDRAWN = 'withdrawn';

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ApplicationNote::class);
    }

    public function evaluationSession(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EvaluationSession::class);
    }

    public function evaluationSessions(): HasMany
    {
        return $this->hasMany(EvaluationSession::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_REJECTED, self::STATUS_WITHDRAWN, self::STATUS_ACCEPTED]);
    }

    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', [self::STATUS_ACCEPTED, self::STATUS_OFFER_EXTENDED]);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('applied_at', 'desc');
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SUBMITTED => 'Application Submitted',
            self::STATUS_VIEWED => 'Viewed by Employer',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_INTERVIEW_SCHEDULED => 'Interview Scheduled',
            self::STATUS_INTERVIEW_COMPLETED => 'Interview Completed',
            self::STATUS_OFFER_EXTENDED => 'Offer Received',
            self::STATUS_ACCEPTED => 'Offer Accepted',
            self::STATUS_REJECTED => 'Application Rejected',
            self::STATUS_WITHDRAWN => 'Application Withdrawn',
            default => 'Unknown Status',
        };
    }

    public function getIsActiveAttribute(): bool
    {
        return !in_array($this->status, [self::STATUS_REJECTED, self::STATUS_WITHDRAWN, self::STATUS_ACCEPTED]);
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_OFFER_EXTENDED]);
    }

    /**
     * Status update helpers
     */
    public function markAsViewed(): void
    {
        if (!$this->viewed_at) {
            $this->update([
                'status' => self::STATUS_VIEWED,
                'viewed_at' => now()
            ]);
        }
    }

    public function markAsShortlisted(): void
    {
        $this->update([
            'status' => self::STATUS_SHORTLISTED,
            'responded_at' => now()
        ]);
    }

    public function scheduleInterview(\DateTime $interviewTime): void
    {
        $this->update([
            'status' => self::STATUS_INTERVIEW_SCHEDULED,
            'interview_at' => $interviewTime,
            'responded_at' => now()
        ]);
    }

    public function markInterviewCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_INTERVIEW_COMPLETED
        ]);
    }

    public function extendOffer(): void
    {
        $this->update([
            'status' => self::STATUS_OFFER_EXTENDED,
            'offer_at' => now(),
            'responded_at' => now()
        ]);
    }

    public function accept(): void
    {
        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'decision_at' => now()
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'decision_at' => now(),
            'responded_at' => now()
        ]);
    }

    public function withdraw(): void
    {
        $this->update([
            'status' => self::STATUS_WITHDRAWN,
            'decision_at' => now()
        ]);
    }
}


