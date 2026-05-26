<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * Central cache manager for invalidating related caches on both sides.
 *
 * Student side: app_stats, job_recommendations
 * Employer side: employer_app_counts, employer_status_counts, employer_weekly/monthly_trend, employer_job_counts
 *
 * Call CacheService::onApplicationChanged() any time an application is
 * created, updated, or deleted so both dashboards reflect the change.
 */
class CacheService
{
    /**
     * Cache durations in seconds
     */
    const CACHE_DURATIONS = [
        'user_profile'          => 3600,
        'company_profile'       => 3600,
        'job_listing'           => 1800,
        'job_details'           => 3600,
        'search_results'        => 900,
        'recommended_jobs'      => 1800,
        'application_count'     => 300,
        'statistics'            => 600,
        'ai_embeddings'         => 86400,
        'ai_response'           => 3600,
        'subscription_features' => 3600,
    ];

    // -----------------------------------------------------------------------
    // Cross-side invalidation — call after any application create/update/delete
    // -----------------------------------------------------------------------

    public static function onApplicationChanged(int $userId, int $companyId): void
    {
        self::bustStudentCaches($userId);
        self::bustEmployerCaches($companyId);
    }

    public static function bustStudentCaches(int $userId): void
    {
        Cache::forget("app_stats_{$userId}");
        Cache::forget("job_recommendations_{$userId}");
    }

    public static function bustEmployerCaches(int $companyId): void
    {
        Cache::forget("employer_job_counts_{$companyId}");
        Cache::forget("employer_app_counts_{$companyId}");
        Cache::forget("employer_status_counts_{$companyId}");
        Cache::forget("employer_weekly_trend_{$companyId}");
        Cache::forget("employer_monthly_trend_{$companyId}");
    }

    public static function onJobChanged(int $companyId): void
    {
        Cache::forget("employer_job_counts_{$companyId}");
        Cache::forget('job_locations');
        Cache::forget('featured_jobs');
    }

    /**
     * Get cached data or execute callback
     */
    public function remember(string $key, string $type, callable $callback)
    {
        $duration = self::CACHE_DURATIONS[$type] ?? 3600;
        return Cache::remember($key, $duration, $callback);
    }

    /**
     * Cache user profile
     */
    public function cacheUserProfile(int $userId, $data): void
    {
        Cache::put(
            "user_profile:{$userId}",
            $data,
            self::CACHE_DURATIONS['user_profile']
        );
    }

    /**
     * Get cached user profile
     */
    public function getUserProfile(int $userId)
    {
        return Cache::get("user_profile:{$userId}");
    }

    /**
     * Invalidate user profile cache
     */
    public function invalidateUserProfile(int $userId): void
    {
        Cache::forget("user_profile:{$userId}");
    }

    /**
     * Cache job listing
     */
    public function cacheJobListing(array $filters, $data): void
    {
        $key = 'job_listing:' . md5(json_encode($filters));
        Cache::put($key, $data, self::CACHE_DURATIONS['job_listing']);
    }

    /**
     * Cache job details
     */
    public function cacheJobDetails(int $jobId, $data): void
    {
        Cache::put(
            "job_details:{$jobId}",
            $data,
            self::CACHE_DURATIONS['job_details']
        );
    }

    /**
     * Invalidate job cache
     */
    public function invalidateJob(int $jobId): void
    {
        Cache::forget("job_details:{$jobId}");
        // Also clear related listings (pattern matching)
        $this->clearPattern('job_listing:*');
        $this->clearPattern("recommended_jobs:*");
    }

    /**
     * Cache search results
     */
    public function cacheSearchResults(string $query, array $filters, $data): void
    {
        $key = 'search:' . md5($query . json_encode($filters));
        Cache::put($key, $data, self::CACHE_DURATIONS['search_results']);
    }

    /**
     * Cache recommended jobs for user
     */
    public function cacheRecommendedJobs(int $userId, $data): void
    {
        Cache::put(
            "recommended_jobs:{$userId}",
            $data,
            self::CACHE_DURATIONS['recommended_jobs']
        );
    }

    /**
     * Cache AI embeddings
     */
    public function cacheEmbeddings(string $content, array $embeddings): void
    {
        $key = 'embeddings:' . md5($content);
        Cache::put($key, $embeddings, self::CACHE_DURATIONS['ai_embeddings']);
    }

    /**
     * Get cached embeddings
     */
    public function getEmbeddings(string $content): ?array
    {
        $key = 'embeddings:' . md5($content);
        return Cache::get($key);
    }

    /**
     * Cache AI response
     */
    public function cacheAIResponse(string $prompt, string $response): void
    {
        $key = 'ai_response:' . md5($prompt);
        Cache::put($key, $response, self::CACHE_DURATIONS['ai_response']);
    }

    /**
     * Get cached AI response
     */
    public function getAIResponse(string $prompt): ?string
    {
        $key = 'ai_response:' . md5($prompt);
        return Cache::get($key);
    }

    /**
     * Cache statistics
     */
    public function cacheStatistics(string $type, int $entityId, $data): void
    {
        $key = "stats:{$type}:{$entityId}";
        Cache::put($key, $data, self::CACHE_DURATIONS['statistics']);
    }

    /**
     * Get cached statistics
     */
    public function getStatistics(string $type, int $entityId)
    {
        return Cache::get("stats:{$type}:{$entityId}");
    }

    /**
     * Cache subscription features
     */
    public function cacheSubscriptionFeatures(int $userId, array $features): void
    {
        Cache::put(
            "subscription_features:{$userId}",
            $features,
            self::CACHE_DURATIONS['subscription_features']
        );
    }

    /**
     * Get cached subscription features
     */
    public function getSubscriptionFeatures(int $userId): ?array
    {
        return Cache::get("subscription_features:{$userId}");
    }

    /**
     * Clear cache by pattern (Redis only)
     */
    public function clearPattern(string $pattern): void
    {
        if (config('cache.default') === 'redis') {
            $redis = Redis::connection();
            $keys = $redis->keys(config('cache.prefix') . ':' . $pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
            }
        } else {
            // For non-Redis drivers, we can't pattern match
            // So we just flush the entire cache (use with caution)
            // Cache::flush();
        }
    }

    /**
     * Warm up cache for popular jobs
     */
    public function warmUpPopularJobs(): void
    {
        // Get top 50 most viewed jobs from last 7 days
        $popularJobs = \App\Models\Job::where('status', 'published')
            ->withCount(['jobViews' => function ($q) {
                $q->where('created_at', '>=', now()->subDays(7));
            }])
            ->orderByDesc('job_views_count')
            ->limit(50)
            ->get();

        foreach ($popularJobs as $job) {
            $this->cacheJobDetails($job->id, $job->load('company'));
        }
    }

    /**
     * Clear user-related caches on profile update
     */
    public function clearUserCaches(int $userId): void
    {
        $this->invalidateUserProfile($userId);
        Cache::forget("recommended_jobs:{$userId}");
        Cache::forget("subscription_features:{$userId}");
        $this->clearPattern("match_analysis:{$userId}:*");
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        if (config('cache.default') === 'redis') {
            $redis = Redis::connection();
            $info = $redis->info();
            
            return [
                'driver' => 'redis',
                'keys' => $redis->dbSize(),
                'memory_used' => $info['used_memory_human'] ?? 'N/A',
                'hit_rate' => isset($info['keyspace_hits'], $info['keyspace_misses']) 
                    ? round(($info['keyspace_hits'] / ($info['keyspace_hits'] + $info['keyspace_misses'])) * 100, 2) 
                    : 0,
            ];
        }
        
        return [
            'driver' => config('cache.default'),
            'message' => 'Statistics only available for Redis',
        ];
    }
}
