<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repairs production drift where `round_attempts` was created (via a historical
 * fake-recorded sync migration) WITHOUT the columns the application relies on,
 * causing "Unknown column 'questions'" 42S22 errors when starting a test.
 *
 * Every column is added behind a hasColumn() guard so this is safe to run on
 * environments where the columns already exist (local/SQLite created by the
 * original create migration).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('round_attempts')) {
            return;
        }

        Schema::table('round_attempts', function (Blueprint $table) {
            if (! Schema::hasColumn('round_attempts', 'application_id')) {
                $table->foreignId('application_id')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('round_attempts', 'questions')) {
                $table->json('questions')->nullable()->after('application_id');
            }

            if (! Schema::hasColumn('round_attempts', 'answers')) {
                $table->json('answers')->nullable()->after('questions');
            }

            if (! Schema::hasColumn('round_attempts', 'score')) {
                $table->unsignedTinyInteger('score')->nullable()->after('answers');
            }

            if (! Schema::hasColumn('round_attempts', 'ai_feedback')) {
                $table->text('ai_feedback')->nullable()->after('score');
            }

            if (! Schema::hasColumn('round_attempts', 'status')) {
                $table->enum('status', ['not_started', 'in_progress', 'submitted', 'evaluated'])
                    ->default('not_started')
                    ->after('ai_feedback');
            }

            if (! Schema::hasColumn('round_attempts', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('round_attempts', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('started_at');
            }
        });
    }

    public function down(): void
    {
        // Intentionally a no-op: this migration only repairs missing columns and
        // dropping them could destroy candidate test data on environments that
        // already had them.
    }
};
