<?php

declare(strict_types=1);

namespace App\Jobs\Agent;

use App\Models\AgentConfiguration;
use App\Models\AgentInternalMatch;
use App\Services\Agent\InternalJobMatcherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Scans all internal platform job listings for every active agent user,
 * scores matches, generates AI cover letters, and stores results in
 * agent_internal_matches for review or immediate application.
 *
 * Runs hourly via the scheduler.
 */
class ScanInternalJobsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 600; // 10 minutes

    public function handle(InternalJobMatcherService $matcher): void
    {
        // Skip if global agent kill switch is active
        if (AgentConfiguration::isGlobalKillSwitchActive()) {
            Log::warning('ScanInternalJobsJob: global kill switch active — skipping.');
            return;
        }

        $configs = AgentConfiguration::where('is_active', true)
            ->where('is_paused', false)
            ->whereNull('emergency_stopped_at')
            ->where('is_globally_stopped', false)
            ->with('user.profile')
            ->get();

        if ($configs->isEmpty()) {
            return;
        }

        Log::info('ScanInternalJobsJob: starting scan', ['agents' => $configs->count()]);

        $totalNewMatches = 0;

        foreach ($configs as $config) {
            $user = $config->user;
            if (!$user) {
                continue;
            }

            try {
                $newMatches = $matcher->scanAndStore($user, $config);
                $totalNewMatches += $newMatches;

                // If auto-submit is on (require_approval = false), apply immediately
                if (!$config->require_approval && $newMatches > 0) {
                    $this->autoApplyApproved($matcher, $config);
                }
            } catch (\Throwable $e) {
                Log::error('ScanInternalJobsJob: error processing user', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        Log::info('ScanInternalJobsJob: scan finished', ['total_new_matches' => $totalNewMatches]);
    }

    /**
     * For users with require_approval=false, submit all fresh approved matches immediately,
     * respecting the daily application limit.
     */
    private function autoApplyApproved(InternalJobMatcherService $matcher, AgentConfiguration $config): void
    {
        $user = $config->user;

        // How many slots remain today
        $dailyLimit  = $config->daily_application_limit ?? 10;
        $appliedToday = $user->applications()
            ->whereDate('created_at', today())
            ->count();
        $slotsLeft = max(0, $dailyLimit - $appliedToday);

        if ($slotsLeft === 0) {
            return;
        }

        $pendingMatches = AgentInternalMatch::where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderByDesc('match_score')
            ->take($slotsLeft)
            ->get();

        foreach ($pendingMatches as $match) {
            try {
                $matcher->applyForMatch($match);
            } catch (\Throwable $e) {
                Log::error('ScanInternalJobsJob: auto-apply failed', [
                    'match_id' => $match->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }
    }
}
