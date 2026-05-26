<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HiringTestAttempt extends Model
{
    protected $fillable = [
        'application_id', 'hiring_test_id', 'stage',
        'answers', 'score', 'passed', 'started_at', 'submitted_at',
    ];

    protected $casts = [
        'answers'      => 'array',
        'score'        => 'integer',
        'passed'       => 'boolean',
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(HiringTest::class, 'hiring_test_id');
    }
}
