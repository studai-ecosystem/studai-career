<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FreelancerGig extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'freelancer_profile_id',
        'title',
        'slug',
        'description',
        'category',
        'packages',
        'tags',
        'faq',
        'requirements',
        'status',
        'is_featured',
        'views_count',
        'orders_count',
        'average_rating',
        'total_reviews',
    ];

    protected $casts = [
        'packages'       => 'array',
        'tags'           => 'array',
        'faq'            => 'array',
        'is_featured'    => 'boolean',
        'average_rating' => 'decimal:2',
    ];

    // ── Accessors (guarantee array even when cast fails) ──────────────

    public function getPackagesAttribute(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    public function getTagsAttribute(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    // ── Computed attributes ───────────────────────────────────────────

    public function getStartingPriceAttribute(): int
    {
        $pkgs = $this->packages;
        if (empty($pkgs)) {
            return 0;
        }
        return (int) min(array_column($pkgs, 'price'));
    }

    public function getPackage(string $type): ?array
    {
        foreach ($this->packages as $pkg) {
            if (($pkg['type'] ?? '') === $type) {
                return $pkg;
            }
        }
        return null;
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query): mixed
    {
        return $query->where('is_featured', true);
    }

    public function scopeCategory($query, string $category): mixed
    {
        return $query->where('category', $category);
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function freelancerProfile(): BelongsTo
    {
        return $this->belongsTo(FreelancerProfile::class);
    }

    // ── Boot ──────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (FreelancerGig $gig): void {
            if (empty($gig->slug)) {
                $gig->slug = Str::slug($gig->title) . '-' . Str::random(6);
            }
        });
    }
}
