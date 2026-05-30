<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Safety-net migration: ensure the gamification system tables exist on production.
 *
 * Addresses the same "sync-recorded but never executed" drift pattern as
 * 2026_05_30_000000_ensure_feature_tables_exist. The original gamification
 * migration (2025_11_27_220000) may have been fake-recorded via the historical
 * bulk-sync commit and therefore never actually created the tables.
 *
 * This migration is a safe no-op everywhere the tables already exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Re-run the canonical gamification migration only when the anchor table
        // is missing (same anchor-guard pattern as the parent safety-net migration).
        if (!Schema::hasTable('user_gamification_profiles')) {
            $this->runMigrationUp(
                database_path('migrations'),
                '2025_11_27_220000_create_gamification_tables.php'
            );
            Log::info('ensure_gamification_tables: created gamification tables (anchor was missing).');
        }

        // Also ensure the GamificationActivity model's table has the action column.
        // Earlier versions of the migration omitted it; add defensively.
        if (Schema::hasTable('gamification_activities') && !Schema::hasColumn('gamification_activities', 'action')) {
            Schema::table('gamification_activities', function (Blueprint $table) {
                $table->string('action')->nullable()->after('user_id');
            });
            Log::info('ensure_gamification_tables: added missing "action" column to gamification_activities.');
        }

        // Ensure video_interview_invitations table has the invitation_token/token routing key.
        // The route uses {invitation:token} but some schema versions only have invitation_token.
        if (Schema::hasTable('video_interview_invitations') && !Schema::hasColumn('video_interview_invitations', 'token')) {
            Schema::table('video_interview_invitations', function (Blueprint $table) {
                $table->string('token')->nullable()->unique()->after('id');
            });
            // Copy existing invitation_token values into the new column
            \Illuminate\Support\Facades\DB::statement(
                'UPDATE video_interview_invitations SET token = invitation_token WHERE token IS NULL AND invitation_token IS NOT NULL'
            );
            Log::info('ensure_gamification_tables: added "token" alias column to video_interview_invitations.');
        }
    }

    public function down(): void
    {
        // Intentionally a no-op: this migration only ensures tables exist and
        // must never drop production data it did not exclusively own.
    }

    private function runMigrationUp(string $dir, string $file): void
    {
        $path = $dir . DIRECTORY_SEPARATOR . $file;

        if (!is_file($path)) {
            Log::warning("ensure_gamification_tables: missing migration file {$file}");
            return;
        }

        try {
            $migration = require $path;
            $migration->up();
        } catch (\Throwable $e) {
            Log::error("ensure_gamification_tables: failed running {$file}: " . $e->getMessage());
        }
    }
};
