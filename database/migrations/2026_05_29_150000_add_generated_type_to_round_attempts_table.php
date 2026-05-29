<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds `generated_type` to `round_attempts` so we can record which hiring-round
 * type a stored question set was generated for. This lets the controller detect
 * and refresh stale question sets (e.g. attempts created before type-specific
 * question banks existed, which caused every round to show the same
 * company-info / generic questions).
 *
 * Guarded with hasColumn() — safe no-op where the column already exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('round_attempts')) {
            return;
        }

        if (! Schema::hasColumn('round_attempts', 'generated_type')) {
            Schema::table('round_attempts', function (Blueprint $table) {
                $table->string('generated_type')->nullable()->after('questions');
            });
        }
    }

    public function down(): void
    {
        // No-op.
    }
};
