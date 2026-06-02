<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\LearningResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * D12: Weekly link-health check for curated learning resources.
 *
 * Iterates every learning resource that has a URL, performs a lightweight
 * reachability probe, and records the outcome on the resource so that broken
 * links can be surfaced for re-curation. Network failures for an individual
 * resource never abort the batch.
 */
class CheckLearningResourceLinksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 1800; // 30 minutes for large catalogues

    public const STATUS_OK = 'ok';
    public const STATUS_BROKEN = 'broken';
    public const STATUS_UNREACHABLE = 'unreachable';

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting weekly learning resource link-health check');

        $stats = [
            'checked' => 0,
            'ok' => 0,
            'broken' => 0,
            'unreachable' => 0,
        ];

        LearningResource::query()
            ->whereNotNull('url')
            ->where('url', '!=', '')
            ->chunkById(100, function ($resources) use (&$stats): void {
                foreach ($resources as $resource) {
                    $result = $this->probe((string) $resource->url);

                    $resource->forceFill([
                        'link_status' => $result['status'],
                        'link_http_status' => $result['http_status'],
                        'link_checked_at' => now(),
                    ])->saveQuietly();

                    $stats['checked']++;
                    $stats[$result['status']] = ($stats[$result['status']] ?? 0) + 1;
                }
            });

        Log::info('Completed weekly learning resource link-health check', $stats);

        if ($stats['broken'] > 0 || $stats['unreachable'] > 0) {
            Log::warning('Learning resources with unhealthy links detected', [
                'broken' => $stats['broken'],
                'unreachable' => $stats['unreachable'],
            ]);
        }
    }

    /**
     * Probe a single URL and classify the result.
     *
     * @return array{status: string, http_status: int|null}
     */
    protected function probe(string $url): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'StudAI-LinkHealthBot/1.0'])
                ->head($url);

            $code = $response->status();

            if ($code >= 200 && $code < 400) {
                return ['status' => self::STATUS_OK, 'http_status' => $code];
            }

            // Some servers reject HEAD; retry once with a GET before flagging.
            $getResponse = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'StudAI-LinkHealthBot/1.0'])
                ->get($url);

            $getCode = $getResponse->status();

            if ($getCode >= 200 && $getCode < 400) {
                return ['status' => self::STATUS_OK, 'http_status' => $getCode];
            }

            return ['status' => self::STATUS_BROKEN, 'http_status' => $getCode];
        } catch (\Throwable $e) {
            Log::debug('Link-health probe failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return ['status' => self::STATUS_UNREACHABLE, 'http_status' => null];
        }
    }
}
