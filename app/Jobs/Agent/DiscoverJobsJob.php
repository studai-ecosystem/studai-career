<?php

namespace App\Jobs\Agent;

use App\Models\AgentConfiguration;
use App\Services\Agent\JobDiscoveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

/**
 * Discover Jobs Job
 * 
 * Runs hourly to discover new job opportunities for all active agents.
 */
class DiscoverJobsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    /**
     * Execute the job.
     */
    public function handle(JobDiscoveryService $discoveryService): void
    {
        Log::info('Starting job discovery for all active agents');

        $activeConfigs = AgentConfiguration::active()
            ->where('is_paused', false)
            ->get();

        Log::info('Found active agent configurations', [
            'count' => $activeConfigs->count(),
        ]);

        foreach ($activeConfigs as $config) {
            try {
                // Check if within active hours
                if (!$this->isWithinActiveHours($config)) {
                    Log::debug('Agent not within active hours', [
                        'config_id' => $config->id,
                        'user_id' => $config->user_id,
                    ]);
                    continue;
                }

                Log::info('Discovering jobs for agent', [
                    'config_id' => $config->id,
                    'user_id' => $config->user_id,
                ]);

                $jobMatches = $discoveryService->discoverJobs($config);

                Log::info('Job discovery completed for agent', [
                    'config_id' => $config->id,
                    'user_id' => $config->user_id,
                    'matches_found' => $jobMatches->count(),
                ]);

                // Update last run timestamp
                $config->update(['last_run_at' => now()]);

                // Queue analysis and submission jobs if matches found
                if ($jobMatches->isNotEmpty()) {
                    // D14: Respect the queue-depth cap so a slow worker or a large
                    // number of active agents can never flood the queue. Skipped
                    // configs are retried on the next scheduled discovery cycle.
                    if ($this->isQueueOverCapacity()) {
                        Log::warning('Agent queue depth cap reached, deferring submission dispatch', [
                            'config_id' => $config->id,
                            'max_queue_depth' => (int) config('agent.max_queue_depth', 250),
                        ]);
                        continue;
                    }

                    SubmitApplicationsJob::dispatch($config)->delay(now()->addMinutes(5));
                }

            } catch (\Exception $e) {
                Log::error('Job discovery failed for agent', [
                    'config_id' => $config->id,
                    'user_id' => $config->user_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::info('Job discovery completed for all agents');
    }

    /**
     * D14: Determine whether the agent queue has reached its configured depth
     * cap. When the underlying driver does not support size introspection we
     * fail open (return false) so discovery is never silently blocked.
     */
    protected function isQueueOverCapacity(): bool
    {
        $cap = (int) config('agent.max_queue_depth', 250);

        if ($cap <= 0) {
            return false;
        }

        try {
            $depth = Queue::size((string) config('agent.queue', 'default'));
        } catch (\Throwable $e) {
            Log::debug('Unable to read agent queue depth, allowing dispatch', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        return $depth >= $cap;
    }

    /**
     * Check if current time is within agent's active hours
     */
    protected function isWithinActiveHours(AgentConfiguration $config): bool
    {
        if (empty($config->active_hours)) {
            return true; // No restrictions
        }

        $currentHour = now()->hour;
        
        foreach ($config->active_hours as $range) {
            if (str_contains($range, '-')) {
                [$start, $end] = explode('-', $range);
                if ($currentHour >= (int)$start && $currentHour <= (int)$end) {
                    return true;
                }
            } elseif ($currentHour == (int)$range) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('DiscoverJobsJob failed permanently', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
