<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates profiles and saved_jobs tables needed by job seeker dashboard.
 * Guarded — safe to run multiple times.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. profiles ───────────────────────────────────────────────────────
        if (! Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('headline')->nullable();
                $table->text('bio')->nullable();
                $table->text('summary')->nullable();
                $table->text('career_goals')->nullable();
                $table->string('phone')->nullable();
                $table->string('location')->nullable();
                $table->string('avatar')->nullable();
                $table->string('resume_path')->nullable();
                $table->json('experience')->nullable();
                $table->json('education')->nullable();
                $table->json('skills')->nullable();
                $table->json('languages')->nullable();
                $table->string('current_location')->nullable();
                $table->json('preferred_locations')->nullable();
                $table->decimal('expected_salary_min', 10, 2)->nullable();
                $table->decimal('expected_salary_max', 10, 2)->nullable();
                $table->string('notice_period')->nullable();
                $table->enum('work_preference', ['remote', 'hybrid', 'onsite'])->nullable();
                $table->json('social_links')->nullable();
                $table->json('job_preferences')->nullable();
                $table->integer('profile_completeness')->default(0);
                $table->boolean('is_public')->default(true);
                $table->boolean('open_to_opportunities')->default(true);
                $table->timestamps();
                $table->index(['user_id', 'is_public']);
            });
        }

        // ── 2. saved_jobs ─────────────────────────────────────────────────────
        if (! Schema::hasTable('saved_jobs')) {
            Schema::create('saved_jobs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('job_id')->constrained('job_listings')->onDelete('cascade');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'job_id']);
                $table->index('created_at');
            });
        }

        // ── 3. ai_credit_logs ─────────────────────────────────────────────────
        if (! Schema::hasTable('ai_credit_logs')) {
            Schema::create('ai_credit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('action');
                $table->integer('credits_used')->default(0);
                $table->json('metadata')->nullable();
                $table->string('model')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
                $table->index('action');
            });
        }
    }

    public function down(): void
    {
        // Left intentionally bare — no destructive rollbacks in production
    }
};
