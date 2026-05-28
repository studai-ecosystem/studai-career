<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * REPAIR MIGRATION — 2026-05-28
 *
 * The ai_credit_logs table was created by fix_missing_profiles_and_saved_jobs
 * with a different schema (no `description` column, used `metadata` instead of `meta`).
 * The model and User::deductAICredits() require both `description` and `meta` columns.
 *
 * Safe to run on any environment (guarded with hasColumn checks).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_credit_logs', function (Blueprint $table): void {
            // Add `description` if missing — this is what causes the production error
            if (! Schema::hasColumn('ai_credit_logs', 'description')) {
                $table->string('description')->after('action')->nullable();
            }

            // Add `meta` if missing (model fillable uses 'meta', not 'metadata')
            if (! Schema::hasColumn('ai_credit_logs', 'meta')) {
                $table->json('meta')->after('credits_used')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ai_credit_logs', function (Blueprint $table): void {
            if (Schema::hasColumn('ai_credit_logs', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('ai_credit_logs', 'meta')) {
                $table->dropColumn('meta');
            }
        });
    }
};
