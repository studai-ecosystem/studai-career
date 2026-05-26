<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VantageSkillAward extends Model
{
    protected $table = 'vantage_skill_awards';

    public const TIER_EMERGING   = 'emerging';
    public const TIER_DEVELOPING = 'developing';
    public const TIER_PROFICIENT = 'proficient';
    public const TIER_ADVANCED   = 'advanced';

    public const TIER_THRESHOLDS = [
        self::TIER_EMERGING   => 2.0,
        self::TIER_DEVELOPING => 3.0,
        self::TIER_PROFICIENT => 4.0,
        self::TIER_ADVANCED   => 4.5,
    ];

    public const TIER_LABELS = [
        self::TIER_EMERGING   => 'Emerging',
        self::TIER_DEVELOPING => 'Developing',
        self::TIER_PROFICIENT => 'Proficient',
        self::TIER_ADVANCED   => 'Advanced',
    ];

    protected $fillable = [
        'user_id',
        'skill',
        'tier',
        'score',
        'source_type',
        'source_id',
        'unlocked_at',
    ];

    protected $casts = [
        'score'       => 'decimal:2',
        'unlocked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
