<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Self-healing migration for production schema drift.
 *
 * A historical "sync" migration (56a9ab0) fake-recorded 110+ older migrations
 * in the `migrations` table so the migrator now SKIPS them, even though some of
 * their tables were never actually created on production MySQL. That leaves the
 * Network, Calendar, Subscriptions, Payments and Offer-letter pages throwing
 * 500s on prod (the tables simply do not exist) while working fine locally.
 *
 * This migration re-invokes the canonical create-migrations' up() methods in
 * dependency order so any MISSING table is created. It is a safe no-op anywhere
 * the tables already exist:
 *   - Networking / Calendar create-migrations are fully self-guarded
 *     (each Schema::create is wrapped in `if (!Schema::hasTable(...))`), so they
 *     are safe to re-invoke directly and handle partially-present schemas.
 *   - The Subscription / Payment / Offer create-migrations are NOT internally
 *     guarded, so we only invoke them when their anchor table is missing
 *     (faked-recorded migrations create the whole table set or none of it).
 */
return new class extends Migration
{
    public function up(): void
    {
        $dir = database_path('migrations');

        // Fully self-guarded canonical migrations — safe to re-invoke directly.
        $selfGuarded = [
            '2025_11_27_300000_create_professional_networking_tables.php',
            '2025_11_27_500000_create_calendar_tables.php',
        ];

        foreach ($selfGuarded as $file) {
            $this->runMigrationUp($dir, $file);
        }

        // Unguarded canonical migrations — only run when the anchor table is
        // missing, to avoid "table already exists" errors where they exist.
        $anchorGuarded = [
            ['2025_10_28_162814_create_subscription_plans_table.php', 'subscription_plans'],
            ['2025_10_28_162820_create_user_subscriptions_table.php', 'user_subscriptions'],
            ['2025_10_28_162824_create_payment_transactions_table.php', 'payment_transactions'],
            ['2025_11_27_900000_create_offer_letter_tables.php', 'offer_letter_templates'],
            ['2026_02_22_000001_create_payment_activity_logs_table.php', 'payment_activity_logs'],
        ];

        foreach ($anchorGuarded as [$file, $anchorTable]) {
            if (Schema::hasTable($anchorTable)) {
                continue;
            }

            $this->runMigrationUp($dir, $file);
        }
    }

    public function down(): void
    {
        // Intentionally a no-op: this migration only ensures tables exist and
        // must never drop production data it did not exclusively own.
    }

    /**
     * Require a canonical migration file and run its up() method, logging (but
     * not rethrowing) failures so one missing dependency cannot abort the rest.
     */
    private function runMigrationUp(string $dir, string $file): void
    {
        $path = $dir . DIRECTORY_SEPARATOR . $file;

        if (!is_file($path)) {
            Log::warning("ensure_feature_tables_exist: missing migration file {$file}");

            return;
        }

        try {
            $migration = require $path;
            $migration->up();
        } catch (\Throwable $e) {
            Log::error("ensure_feature_tables_exist: failed running {$file}: " . $e->getMessage());
        }
    }
};
