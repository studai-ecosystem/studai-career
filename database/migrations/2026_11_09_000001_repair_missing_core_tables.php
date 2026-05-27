<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repair migration: idempotently recreates core tables that may have been
 * lost while already recorded in the migrations table.  Safe to run multiple
 * times – every block is guarded by Schema::hasTable / Schema::hasColumn.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. subscription_plans ────────────────────────────────────────────
        // Schema matches SubscriptionPlan model $fillable + SubscriptionPlanSeeder.
        if (! Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('razorpay_plan_id')->nullable();
                $table->string('payu_plan_id')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->string('currency', 3)->default('INR');
                $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly');
                $table->json('features')->nullable();
                $table->integer('ai_credits')->default(0);
                $table->integer('applications_limit')->nullable();
                $table->integer('job_alerts_limit')->nullable();
                $table->boolean('priority_support')->default(false);
                $table->boolean('api_access')->default(false);
                $table->integer('api_calls_limit')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index('slug');
                $table->index(['is_active', 'sort_order']);
            });
        }

        // ── 2. notifications (Laravel default notifications table) ───────────
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // ── 3. resume_templates ──────────────────────────────────────────────
        if (! Schema::hasTable('resume_templates')) {
            Schema::create('resume_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('preview_image')->nullable();
                $table->enum('category', ['professional', 'creative', 'modern', 'minimalist', 'academic', 'executive']);
                $table->enum('industry', ['technology', 'healthcare', 'finance', 'education', 'creative', 'general']);
                $table->json('color_scheme');
                $table->json('layout_config');
                $table->boolean('is_ats_friendly')->default(true);
                $table->boolean('is_premium')->default(false);
                $table->integer('popularity_score')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['category', 'is_active']);
                $table->index(['industry', 'is_active']);
            });
        }

        // ── 4. companies (base + all additive column migrations) ─────────────
        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                // review stats (from 2025_11_27_100004)
                $table->decimal('avg_rating', 3, 2)->nullable();
                $table->unsignedInteger('total_reviews')->default(0);
                $table->unsignedInteger('total_salaries')->default(0);
                $table->unsignedInteger('total_interviews')->default(0);
                $table->unsignedTinyInteger('recommend_percent')->nullable();
                $table->unsignedTinyInteger('ceo_approval_percent')->nullable();
                $table->decimal('avg_salary', 12, 2)->nullable();
                $table->decimal('interview_difficulty_avg', 3, 2)->nullable();
                // contact fields (from 2026_05_12_100000)
                $table->string('company_email')->nullable();
                $table->string('hr_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('logo')->nullable();
                $table->string('logo_url')->nullable();      // 2026_05_23
                $table->string('website')->nullable();
                $table->string('linkedin_url')->nullable();  // 2026_05_23
                $table->string('industry')->nullable();
                $table->string('company_size')->nullable();
                $table->year('founded_year')->nullable();
                $table->string('headquarters')->nullable();
                $table->text('culture')->nullable();         // 2026_05_23
                $table->float('culture_rating')->nullable();
                $table->json('locations')->nullable();
                $table->json('benefits')->nullable();
                $table->json('tech_stack')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->softDeletes();                       // 2025_11_18
                $table->timestamps();

                $table->index(['slug', 'is_verified']);
            });
        }
    }

    public function down(): void
    {
        // Only drop tables that did not exist before this migration ran.
        // Since we cannot know that at rollback time, we leave tables intact.
    }
};
