<?php

declare(strict_types=1);

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Centralised operational alerting for AI pipeline events.
 *
 * Always writes a structured log entry; optionally posts to a Slack webhook
 * when configured. Used for: AI provider fallback, ranking blocked on missing
 * inputs, and per-job cost ceiling breaches.
 */
class OpsAlertService
{
    /**
     * Raise an operational alert.
     *
     * @param string               $event   Short machine-readable event key (e.g. 'ai.fallback').
     * @param string               $message Human-readable summary.
     * @param array<string, mixed> $context Structured context for the log entry.
     */
    public static function alert(string $event, string $message, array $context = []): void
    {
        if (! config('ai.ops_alerts.enabled', true)) {
            return;
        }

        $channel = config('ai.ops_alerts.log_channel', 'stack');
        $payload = array_merge(['event' => $event], $context);

        try {
            Log::channel($channel)->error("[OPS ALERT] {$event}: {$message}", $payload);
        } catch (\Throwable) {
            // Fall back to the default channel if the configured one is unavailable.
            Log::error("[OPS ALERT] {$event}: {$message}", $payload);
        }

        self::postToSlack($event, $message, $context);
    }

    /**
     * Post the alert to Slack when a webhook is configured. Best-effort only.
     *
     * @param array<string, mixed> $context
     */
    private static function postToSlack(string $event, string $message, array $context): void
    {
        $webhook = config('ai.ops_alerts.slack_webhook');
        if (empty($webhook)) {
            return;
        }

        $contextLine = self::formatContext($context);
        $text = ":rotating_light: *{$event}*\n{$message}";
        if ($contextLine !== '') {
            $text .= "\n```{$contextLine}```";
        }

        try {
            Http::timeout(5)->post($webhook, ['text' => $text]);
        } catch (\Throwable $e) {
            Log::warning('OpsAlertService: Slack post failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @param array<string, mixed> $context
     */
    private static function formatContext(array $context): string
    {
        if ($context === []) {
            return '';
        }

        $encoded = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return $encoded === false ? '' : $encoded;
    }
}
