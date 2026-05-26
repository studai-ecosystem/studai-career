<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkEmailLog extends Model
{
    protected $fillable = [
        'job_id', 'email_type', 'total_recipients', 'sent_count',
        'failed_count', 'status', 'started_at', 'completed_at', 'failed_recipients',
    ];

    protected $casts = [
        'failed_recipients' => 'array',
        'started_at'        => 'datetime',
        'completed_at'      => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }
}
