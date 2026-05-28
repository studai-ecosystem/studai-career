<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIDisclaimerAcknowledgment extends Model
{
    protected $fillable = [
        'disclaimer_id', 'user_id',
        'subject_type', 'subject_id',
        'ip_address', 'user_agent',
        'acknowledged_at',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    public function disclaimer(): BelongsTo
    {
        return $this->belongsTo(AIDisclaimer::class, 'disclaimer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
