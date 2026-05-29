<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the `violations` column to `round_attempts` on environments where the
 * historical "add_violations_to_round_attempts_table" migration was
 * fake-recorded (never actually executed) — causing a 500 ("Unknown column
 * 'violations'") when submitting a candidate test.
 *
 * Guarded with hasColumn() so it is a safe no-op where the column already exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('round_attempts')) {
            return;
        }

        if (! Schema::hasColumn('round_attempts', 'violations')) {
            Schema::table('round_attempts', function (Blueprint $table) {
                $table->unsignedSmallInteger('violations')->default(0)->after('submitted_at');
            });
        }
    }

    public function down(): void
    {
        // No-op: dropping the column could destroy proctoring data.
    }
};
