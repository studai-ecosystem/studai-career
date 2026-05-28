<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This migration ensures the cover_letters table exists.
 *
 * The 2026_05_22_081832_create_cover_letters_table migration was marked as
 * "already run" in sync_historical_migrations without actually creating the
 * table. This migration fixes that by creating it if it doesn't exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cover_letters')) {
            return; // Already exists — nothing to do
        }

        Schema::create('cover_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('tone')->default('professional');
            $table->string('target_role')->nullable();
            $table->string('target_company')->nullable();
            $table->longText('content');
            $table->longText('content_html')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Intentionally no-op: this migration is a repair migration.
        // Dropping the table would cause data loss.
    }
};
