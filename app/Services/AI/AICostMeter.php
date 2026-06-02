<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Services\Monitoring\OpsAlertService;
use Illuminate\Support\Facades\Cache;

/**
 * I3: Per-hire unit economics / cost ceiling.
 *
 * Tracks cumulative AI spend per job (primarily Stage 4 evaluation generation)
 * in a cache-backed meter and enforces a soft per-job ceiling. When the ceiling
 * is breached the pipeline stops spending on new generation and prefers cache /
 * existing-bank reuse, raising an ops alert so spend can be investigated.
 */
final class AICostMeter
{
    private const CACHE_PREFIX = 'ai_cost_job_';
    private const TTL_DAYS = 45;

    /**
     * Current accumulated AI spend (USD) recorded for a job.
     */
    public static function jobSpend(int $jobId): float
    {
        return (float) Cache::get(self::key($jobId), 0.0);
    }

    /**
     * Soft ceiling (USD) configured per job.
     */
    public static function ceiling(): float
    {
        return (float) config('ai.cost.per_job_ceiling_usd', 50.0);
    }

    /**
     * Whether the job has already reached or exceeded its spend ceiling.
     */
    public static function ceilingExceeded(int $jobId): bool
    {
        return self::jobSpend($jobId) >= self::ceiling();
    }

    /**
     * Whether adding $usd would push the job past its ceiling.
     */
    public static function wouldExceed(int $jobId, float $usd): bool
    {
        return (self::jobSpend($jobId) + $usd) > self::ceiling();
    }

    /**
     * Record additional AI spend for a job and return the new running total.
     * Emits an ops alert when the configured alert threshold is crossed.
     */
    public static function record(int $jobId, float $usd): float
    {
        $previous = self::jobSpend($jobId);
        $total = round($previous + max(0.0, $usd), 6);

        Cache::put(self::key($jobId), $total, now()->addDays(self::TTL_DAYS));

        $ceiling = self::ceiling();
        $alertPct = (float) config('ai.cost.alert_at_pct', 80);
        $alertThreshold = $ceiling * ($alertPct / 100);

        if ($previous < $alertThreshold && $total >= $alertThreshold) {
            OpsAlertService::alert(
                'ai.cost.threshold_reached',
                "AI spend for job {$jobId} reached {$alertPct}% of the per-job ceiling.",
                ['job_id' => $jobId, 'spend_usd' => $total, 'ceiling_usd' => $ceiling]
            );
        }

        return $total;
    }

    private static function key(int $jobId): string
    {
        return self::CACHE_PREFIX . $jobId;
    }

    // ── E11: Per-session (mock interview) soft budget ────────────────────────

    private const SESSION_CACHE_PREFIX = 'ai_cost_session_';
    private const SESSION_TTL_HOURS = 12;

    /**
     * Current accumulated AI spend (USD) recorded for an interview session.
     */
    public static function sessionSpend(int $sessionId): float
    {
        return (float) Cache::get(self::sessionKey($sessionId), 0.0);
    }

    /**
     * Soft per-session ceiling (USD).
     */
    public static function sessionCeiling(): float
    {
        return (float) config('ai.cost.per_mock_session_ceiling_usd', 1.5);
    }

    /**
     * Whether the session has already reached or exceeded its spend ceiling.
     */
    public static function sessionBudgetExceeded(int $sessionId): bool
    {
        return self::sessionSpend($sessionId) >= self::sessionCeiling();
    }

    /**
     * Record additional AI spend for an interview session and return the new
     * running total.
     */
    public static function recordSession(int $sessionId, float $usd): float
    {
        $total = round(self::sessionSpend($sessionId) + max(0.0, $usd), 6);

        Cache::put(self::sessionKey($sessionId), $total, now()->addHours(self::SESSION_TTL_HOURS));

        return $total;
    }

    private static function sessionKey(int $sessionId): string
    {
        return self::SESSION_CACHE_PREFIX . $sessionId;
    }
}
