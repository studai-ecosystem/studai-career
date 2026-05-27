<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCTION COMPREHENSIVE FIX MIGRATION
 *
 * The previous repair migration (2026_11_09_000001) was accidentally fake-recorded
 * by the sync migration and never actually ran. This migration creates ALL missing
 * tables needed for the three dashboards to work:
 *   - /studai (admin)
 *   - /dashboard (job seeker)
 *   - /employer/dashboard (employer)
 *
 * Every block is guarded by Schema::hasTable / Schema::hasColumn — safe to run
 * multiple times and on fresh installs.
 *
 * Also ensures the company_id column exists in users (needed for employer middleware).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. subscription_plans ────────────────────────────────────────────
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

        // ── 2. notifications ─────────────────────────────────────────────────
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

        // ── 4. companies ─────────────────────────────────────────────────────
        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->decimal('avg_rating', 3, 2)->nullable();
                $table->unsignedInteger('total_reviews')->default(0);
                $table->unsignedInteger('total_salaries')->default(0);
                $table->unsignedInteger('total_interviews')->default(0);
                $table->unsignedTinyInteger('recommend_percent')->nullable();
                $table->unsignedTinyInteger('ceo_approval_percent')->nullable();
                $table->decimal('avg_salary', 12, 2)->nullable();
                $table->decimal('interview_difficulty_avg', 3, 2)->nullable();
                $table->string('company_email')->nullable();
                $table->string('hr_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('logo')->nullable();
                $table->string('logo_url')->nullable();
                $table->string('website')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->string('industry')->nullable();
                $table->string('company_size')->nullable();
                $table->year('founded_year')->nullable();
                $table->string('headquarters')->nullable();
                $table->text('culture')->nullable();
                $table->float('culture_rating')->nullable();
                $table->json('locations')->nullable();
                $table->json('benefits')->nullable();
                $table->json('tech_stack')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->softDeletes();
                $table->timestamps();
                $table->index(['slug', 'is_verified']);
            });
        }

        // ── 5. user_subscriptions ────────────────────────────────────────────
        if (! Schema::hasTable('user_subscriptions')) {
            Schema::create('user_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
                $table->enum('payment_gateway', ['razorpay', 'payu', 'stripe'])->nullable();
                $table->string('gateway_subscription_id')->nullable();
                $table->enum('status', ['active', 'canceled', 'expired', 'trialing'])->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('current_period_start')->nullable();
                $table->timestamp('current_period_end')->nullable();
                $table->integer('applications_used_this_month')->default(0);
                $table->integer('ai_credits_used_this_month')->default(0);
                $table->timestamp('canceled_at')->nullable();
                $table->timestamp('grace_period_ends_at')->nullable();
                $table->timestamp('next_billing_date')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
                $table->index('ends_at');
            });
        }

        // ── 6. job_listings (full schema with all additive columns) ──────────
        if (! Schema::hasTable('job_listings')) {
            Schema::create('job_listings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('posted_by')->nullable();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description');
                $table->text('responsibilities')->nullable();
                $table->text('qualifications')->nullable();
                $table->string('location')->nullable();
                $table->string('location_type', 50)->nullable();
                $table->string('work_mode')->default('on-site');
                $table->string('employment_type')->default('full-time');
                $table->string('job_type', 50)->nullable();
                $table->string('experience_level')->nullable();
                $table->string('salary_range')->nullable();
                $table->decimal('salary_min', 15, 2)->nullable();
                $table->decimal('salary_max', 15, 2)->nullable();
                $table->string('salary_currency', 10)->default('USD');
                $table->string('salary_period', 20)->default('year');
                $table->json('required_skills')->nullable();
                $table->json('preferred_skills')->nullable();
                $table->json('requirements')->nullable();
                $table->json('benefits')->nullable();
                $table->string('application_method', 30)->default('platform');
                $table->string('external_url')->nullable();
                $table->string('application_email')->nullable();
                $table->text('application_instructions')->nullable();
                $table->enum('status', ['draft', 'published', 'closed', 'archived'])->default('draft');
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_urgent')->default(false);
                // Orin™ application link token
                $table->string('application_link_token', 32)->nullable()->unique();
                // Orin™ lifecycle dates
                $table->date('open_date')->nullable();
                $table->date('close_date')->nullable();
                $table->date('eval_start_date')->nullable();
                $table->date('final_date')->nullable();
                $table->unsignedInteger('target_hire_count')->default(1);
                // Orin™ generated content
                $table->json('orin_generated_jd')->nullable();
                $table->json('orin_application_form_fields')->nullable();
                $table->enum('application_phase', ['draft', 'open', 'closed', 'evaluating', 'ranked', 'complete'])->default('draft');
                $table->boolean('requires_portfolio')->default(false);
                $table->boolean('requires_github')->default(false);
                $table->boolean('requires_work_sample')->default(false);
                $table->json('mandatory_screening_questions')->nullable();
                // Timestamps
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamp('filled_at')->nullable();
                // Counters
                $table->unsignedInteger('views_count')->default(0);
                $table->unsignedInteger('applications_count')->default(0);
                $table->unsignedInteger('saves_count')->default(0);
                // Search / AI
                $table->text('search_keywords')->nullable();
                $table->json('ai_embeddings')->nullable();
                $table->timestamps();
                $table->softDeletes();
                // Indexes
                $table->index(['company_id', 'status']);
                $table->index(['status', 'expires_at']);
                $table->index(['close_date', 'application_phase']);
                $table->index(['eval_start_date', 'application_phase']);
            });
        }

        // ── 7. applications (full schema with all additive columns) ──────────
        if (! Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_id')->constrained('job_listings')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('application_number')->unique();
                $table->text('cover_letter')->nullable();
                $table->string('resume_file')->nullable();
                $table->json('answers')->nullable();
                $table->enum('status', [
                    'draft', 'submitted', 'pending', 'viewed', 'reviewing',
                    'shortlisted', 'interview_scheduled', 'interviewed',
                    'offered', 'accepted', 'rejected', 'withdrawn', 'hired',
                ])->default('draft');
                // Hiring pipeline
                $table->string('hiring_stage')->nullable();
                $table->date('pipeline_stage_date')->nullable();
                $table->text('pipeline_stage_notes')->nullable();
                $table->boolean('confirmation_email_sent')->default(false);
                // Status tracking
                $table->timestamp('status_updated_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->text('ai_rejection_reason')->nullable();
                // AI match
                $table->integer('match_score')->nullable();
                $table->json('match_analysis')->nullable();
                // Evaluation (Orin™)
                $table->enum('evaluation_status', ['pending', 'invited', 'in_progress', 'completed', 'expired', 'skipped'])->default('pending');
                $table->decimal('evaluation_score', 5, 2)->nullable();
                $table->decimal('skill_match_score', 5, 2)->nullable();
                $table->decimal('resume_quality_score', 5, 2)->nullable();
                $table->decimal('behavioural_fit_score', 5, 2)->nullable();
                $table->decimal('final_rank_score', 5, 2)->nullable();
                $table->unsignedInteger('rank_position')->nullable();
                $table->timestamp('evaluation_started_at')->nullable();
                $table->timestamp('evaluation_completed_at')->nullable();
                // Notification tracking
                $table->boolean('application_email_sent')->default(false);
                $table->boolean('evaluation_invite_sent')->default(false);
                $table->boolean('result_email_sent')->default(false);
                $table->timestamp('result_notified_at')->nullable();
                // Applicant materials
                $table->string('portfolio_url')->nullable();
                $table->string('github_url')->nullable();
                $table->string('work_sample_url')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->json('screening_answers')->nullable();
                // Guest applicant support
                $table->string('access_token', 64)->nullable()->unique();
                $table->boolean('is_guest_applicant')->default(false);
                $table->string('guest_name')->nullable();
                $table->string('guest_email')->nullable();
                $table->string('guest_phone')->nullable();
                // Misc
                $table->boolean('is_archived')->default(false);
                $table->string('source')->nullable();
                $table->json('timeline')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('viewed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                // Indexes
                $table->unique(['job_id', 'user_id']);
                $table->index(['user_id', 'status']);
                $table->index(['job_id', 'status']);
                $table->index('evaluation_status');
                $table->index('final_rank_score');
            });
        }

        // ── 8. Ensure users.company_id column exists ─────────────────────────
        if (! Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            });
        }

        // ── 9. Ensure users.account_type column exists ───────────────────────
        if (! Schema::hasColumn('users', 'account_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('account_type', ['job_seeker', 'employer', 'admin'])
                    ->default('job_seeker')
                    ->after('password');
            });
        }

        // ── 10. Ensure users.is_active column exists ─────────────────────────
        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('account_type');
            });
        }
    }

    public function down(): void
    {
        // Intentionally left bare — reverse only on fresh installs
    }
};
