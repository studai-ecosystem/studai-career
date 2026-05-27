<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_id',
        'job_title',
        'department',
        'location',
        'years_of_experience',
        'years_at_company',
        'experience_level',
        'base_salary',
        'bonus',
        'stock_options',
        'signing_bonus',
        'profit_sharing',
        'commission',
        'pay_period',
        'currency',
        'is_current_employee',
        'employment_type',
        'employment_start_date',
        'employment_end_date',
        'benefits',
        'additional_notes',
        'is_verified',
        'status',
        'is_anonymous',
    ];

    protected function casts(): array
    {
        return [
            'base_salary'           => 'decimal:2',
            'bonus'                 => 'decimal:2',
            'stock_options'         => 'decimal:2',
            'signing_bonus'         => 'decimal:2',
            'profit_sharing'        => 'decimal:2',
            'commission'            => 'decimal:2',
            'total_compensation'    => 'decimal:2',
            'benefits'              => 'array',
            'is_current_employee'   => 'boolean',
            'is_verified'           => 'boolean',
            'is_anonymous'          => 'boolean',
            'employment_start_date' => 'date',
            'employment_end_date'   => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
