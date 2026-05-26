<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Services\VantageBadgeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * CheckAndAwardSkillBadges
 *
 * Dispatched after any session completes with a Vantage skill map.
 * Runs asynchronously on the queue so it doesn't block the response.
 */
class CheckAndAwardSkillBadges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries  = 3;
    public int $timeout = 60;

    public function __construct(
        private readonly User   $user,
        private readonly string $sourceType,
        private readonly int    $sourceId,
        private readonly array  $skillMap,
    ) {}

    public function handle(VantageBadgeService $badgeService): void
    {
        $awards = $badgeService->checkAndAward(
            $this->user,
            $this->sourceType,
            $this->sourceId,
            $this->skillMap,
        );

        if (!empty($awards)) {
            Log::info('Vantage awards dispatched', [
                'user_id' => $this->user->id,
                'awards'  => array_map(fn ($a) => $a->skill . ':' . $a->tier, $awards),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CheckAndAwardSkillBadges failed', [
            'user_id'    => $this->user->id,
            'source'     => $this->sourceType . '#' . $this->sourceId,
            'error'      => $exception->getMessage(),
        ]);
    }
}
