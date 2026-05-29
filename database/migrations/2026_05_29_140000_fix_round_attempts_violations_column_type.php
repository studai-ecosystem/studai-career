<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fixes a schema conflict on `round_attempts.violations`.
 *
 * On some installs the table was created by the "fix_application_create_flow"
 * migration, which defined `violations` as a JSON column. The application code
 * (RoundAttempt model + CandidateTestController::submit) treats `violations` as
 * an integer proctoring counter, so writing an int into the JSON column throws
 * "SQLSTATE[22032] Invalid JSON text ... in value for column violations" — a 500
 * when submitting a candidate test.
 *
 * This migration converts the column to an unsigned small integer where it is
 * currently JSON. MySQL-only; a safe no-op on other drivers (e.g. SQLite, where
 * the column is already an integer).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('round_attempts') || ! Schema::hasColumn('round_attempts', 'violations')) {
            return;
        }

        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $column = DB::selectOne(
            "SELECT DATA_TYPE FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'round_attempts'
               AND COLUMN_NAME = 'violations'"
        );

        if ($column && strtolower($column->DATA_TYPE) === 'json') {
            DB::statement('ALTER TABLE `round_attempts` DROP COLUMN `violations`');
            DB::statement('ALTER TABLE `round_attempts` ADD COLUMN `violations` SMALLINT UNSIGNED NOT NULL DEFAULT 0');
        }
    }

    public function down(): void
    {
        // No-op: reverting to a JSON column would reintroduce the bug.
    }
};
