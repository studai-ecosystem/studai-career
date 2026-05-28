<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * REPAIR MIGRATION - 2026-05-28
 *
 * Creates tables that were recorded as "run" in the migrations table
 * but never actually created in production:
 *   - resumes
 *   - resume_ai_suggestions
 *   - resume_versions
 *   - resume_analytics
 *   - ai_resume_generations
 *   - resume_keywords
 *   - user_skills
 *   - skill_gaps
 *   - learning_paths
 *   - learning_resources
 *   - skill_assessments
 *
 * All blocks are guarded by Schema::hasTable — safe to run on any environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. resumes ─────────────────────────────────────────────────────────
        if (! Schema::hasTable('resumes')) {
            Schema::create('resumes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('template_id')->nullable();
                $table->string('title');
                $table->string('slug')->unique();
                $table->boolean('is_default')->default(false);
                $table->string('full_name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('location')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->string('github_url')->nullable();
                $table->string('portfolio_url')->nullable();
                $table->string('profile_photo')->nullable();
                $table->text('professional_summary')->nullable();
                $table->boolean('summary_is_ai_generated')->default(false);
                $table->json('experience')->nullable();
                $table->json('education')->nullable();
                $table->json('skills')->nullable();
                $table->json('certifications')->nullable();
                $table->json('projects')->nullable();
                $table->json('achievements')->nullable();
                $table->json('languages')->nullable();
                $table->json('volunteer_work')->nullable();
                $table->json('publications')->nullable();
                $table->json('custom_sections')->nullable();
                $table->unsignedBigInteger('target_job_id')->nullable();
                $table->text('target_role_description')->nullable();
                $table->json('ai_optimization_data')->nullable();
                $table->timestamp('last_ai_optimized_at')->nullable();
                $table->json('color_overrides')->nullable();
                $table->json('section_order')->nullable();
                $table->json('visibility_settings')->nullable();
                $table->string('pdf_path')->nullable();
                $table->string('docx_path')->nullable();
                $table->string('share_token')->nullable()->unique();
                $table->boolean('is_public')->default(false);
                $table->integer('view_count')->default(0);
                $table->integer('download_count')->default(0);
                $table->enum('ats_score', ['poor', 'fair', 'good', 'excellent'])->nullable();
                $table->json('ats_analysis')->nullable();
                $table->timestamp('last_exported_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'is_default']);
                $table->index(['user_id', 'created_at']);
                $table->index('share_token');
            });
        }

        // ── 2. resume_ai_suggestions ───────────────────────────────────────────
        if (! Schema::hasTable('resume_ai_suggestions')) {
            Schema::create('resume_ai_suggestions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
                $table->enum('section', ['summary', 'experience', 'skills', 'achievements', 'projects']);
                $table->enum('suggestion_type', ['improvement', 'keyword', 'quantification', 'action_verb', 'ats_optimization']);
                $table->text('original_content');
                $table->text('suggested_content');
                $table->text('reasoning')->nullable();
                $table->integer('confidence_score')->default(0);
                $table->enum('status', ['pending', 'accepted', 'rejected', 'modified'])->default('pending');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['resume_id', 'status']);
                $table->index(['resume_id', 'section']);
            });
        }

        // ── 3. resume_versions ─────────────────────────────────────────────────
        if (! Schema::hasTable('resume_versions')) {
            Schema::create('resume_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
                $table->integer('version_number');
                $table->json('resume_data');
                $table->string('change_description')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->index(['resume_id', 'version_number']);
            });
        }

        // ── 4. resume_analytics ────────────────────────────────────────────────
        if (! Schema::hasTable('resume_analytics')) {
            Schema::create('resume_analytics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
                $table->enum('event_type', ['created', 'viewed', 'exported', 'shared', 'customized', 'ai_optimized']);
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('referrer')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at');

                $table->index(['resume_id', 'event_type', 'created_at']);
            });
        }

        // ── 5. ai_resume_generations ───────────────────────────────────────────
        if (! Schema::hasTable('ai_resume_generations')) {
            Schema::create('ai_resume_generations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('resume_id')->nullable();
                $table->enum('generation_type', ['summary', 'experience_bullet', 'skills_extraction', 'achievement_quantification', 'full_resume']);
                $table->text('input_data');
                $table->text('prompt_used');
                $table->text('ai_response');
                $table->integer('tokens_used')->default(0);
                $table->float('cost', 8, 4)->default(0);
                $table->float('generation_time')->default(0);
                $table->string('model_used')->default('gpt-4');
                $table->boolean('was_accepted')->default(false);
                $table->json('feedback')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'generation_type', 'created_at']);
                $table->index('created_at');
            });
        }

        // ── 6. resume_keywords ─────────────────────────────────────────────────
        if (! Schema::hasTable('resume_keywords')) {
            Schema::create('resume_keywords', function (Blueprint $table) {
                $table->id();
                $table->string('keyword');
                $table->string('category');
                $table->string('industry')->nullable();
                $table->string('job_role')->nullable();
                $table->integer('importance_score')->default(0);
                $table->json('synonyms')->nullable();
                $table->integer('usage_count')->default(0);
                $table->timestamps();

                $table->unique(['keyword', 'category']);
                $table->index(['category', 'industry']);
                $table->index('importance_score');
            });
        }

        // ── 7. user_skills ─────────────────────────────────────────────────────
        if (! Schema::hasTable('user_skills')) {
            Schema::create('user_skills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('skill_name');
                $table->string('category')->nullable();
                $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
                $table->integer('proficiency_score')->default(0);
                $table->enum('source', ['self_reported', 'validated', 'ai_detected', 'assessment'])->default('self_reported');
                $table->json('evidence')->nullable();
                $table->date('acquired_date')->nullable();
                $table->date('last_used_date')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->integer('market_demand_score')->nullable();
                $table->decimal('average_salary_impact', 10, 2)->nullable();
                $table->json('related_skills')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'skill_name']);
                $table->index(['user_id', 'category']);
                $table->index('proficiency_level');
                $table->index('is_verified');
                $table->index('market_demand_score');
                $table->unique(['user_id', 'skill_name']);
            });
        }

        // ── 8. skill_gaps ──────────────────────────────────────────────────────
        if (! Schema::hasTable('skill_gaps')) {
            Schema::create('skill_gaps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('skill_name');
                $table->string('category')->nullable();
                $table->enum('gap_severity', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->integer('impact_score')->default(0);
                $table->integer('market_demand_score')->default(0);
                $table->decimal('salary_impact', 10, 2)->nullable();
                $table->json('required_for_roles')->nullable();
                $table->integer('learning_time_weeks')->nullable();
                $table->enum('difficulty_level', ['easy', 'moderate', 'challenging', 'advanced'])->default('moderate');
                $table->json('prerequisite_skills')->nullable();
                $table->json('ai_reasoning')->nullable();
                $table->boolean('is_emerging_skill')->default(false);
                $table->integer('trend_score')->nullable();
                $table->string('trend_direction')->nullable();
                $table->date('identified_date');
                $table->date('target_completion_date')->nullable();
                $table->enum('status', ['identified', 'learning', 'completed', 'deferred'])->default('identified');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'gap_severity']);
                $table->index(['user_id', 'status']);
                $table->index('impact_score');
                $table->index('market_demand_score');
                $table->index('is_emerging_skill');
            });
        }

        // ── 9. learning_paths ──────────────────────────────────────────────────
        if (! Schema::hasTable('learning_paths')) {
            Schema::create('learning_paths', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('skill_gap_id')->nullable();
                $table->string('path_name');
                $table->text('description')->nullable();
                $table->string('target_skill');
                $table->enum('target_proficiency', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
                $table->integer('total_duration_hours')->default(0);
                $table->integer('total_resources')->default(0);
                $table->integer('completed_resources')->default(0);
                $table->decimal('completion_percentage', 5, 2)->default(0);
                $table->json('learning_style_preferences')->nullable();
                $table->json('schedule_preferences')->nullable();
                $table->json('steps')->nullable();
                $table->json('prerequisites_completed')->nullable();
                $table->enum('difficulty_progression', ['gradual', 'moderate', 'steep'])->default('gradual');
                $table->integer('estimated_completion_weeks')->nullable();
                $table->date('started_date')->nullable();
                $table->date('target_completion_date')->nullable();
                $table->date('completed_date')->nullable();
                $table->enum('status', ['draft', 'active', 'paused', 'completed', 'abandoned'])->default('draft');
                $table->boolean('is_ai_generated')->default(true);
                $table->json('ai_customizations')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'status']);
                $table->index('target_skill');
                $table->index('completion_percentage');
            });
        }

        // ── 10. learning_resources ─────────────────────────────────────────────
        if (! Schema::hasTable('learning_resources')) {
            Schema::create('learning_resources', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('learning_path_id')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('url')->nullable();
                $table->enum('resource_type', ['course', 'video', 'article', 'book', 'tutorial', 'project', 'documentation', 'podcast', 'interactive'])->default('article');
                $table->enum('provider', ['coursera', 'udemy', 'pluralsight', 'youtube', 'medium', 'github', 'official_docs', 'free_code_camp', 'khan_academy', 'other'])->default('other');
                $table->string('provider_name')->nullable();
                $table->decimal('cost', 8, 2)->default(0);
                $table->string('currency', 3)->default('USD');
                $table->boolean('is_free')->default(true);
                $table->integer('duration_hours')->nullable();
                $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('beginner');
                $table->decimal('rating', 3, 2)->nullable();
                $table->integer('reviews_count')->nullable();
                $table->json('skills_covered')->nullable();
                $table->string('language', 10)->default('en');
                $table->boolean('has_certificate')->default(false);
                $table->boolean('is_hands_on')->default(false);
                $table->json('prerequisites')->nullable();
                $table->integer('step_order')->default(0);
                $table->integer('ai_relevance_score')->nullable();
                $table->json('tags')->nullable();
                $table->date('last_updated')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['learning_path_id', 'step_order']);
                $table->index('resource_type');
                $table->index('is_free');
                $table->index('difficulty_level');
                $table->index('ai_relevance_score');
            });
        }

        // ── 11. skill_assessments ──────────────────────────────────────────────
        if (! Schema::hasTable('skill_assessments')) {
            Schema::create('skill_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('user_skill_id')->nullable();
                $table->string('skill_name');
                $table->string('assessment_title');
                $table->text('description')->nullable();
                $table->enum('assessment_type', ['multiple_choice', 'coding', 'scenario_based', 'project', 'mixed'])->default('multiple_choice');
                $table->integer('total_questions')->default(0);
                $table->integer('duration_minutes')->default(30);
                $table->json('questions')->nullable();
                $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
                $table->boolean('is_ai_generated')->default(true);
                $table->enum('status', ['draft', 'active', 'completed', 'expired'])->default('draft');
                $table->integer('passing_score')->default(70);
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'skill_name']);
                $table->index(['user_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_assessments');
        Schema::dropIfExists('learning_resources');
        Schema::dropIfExists('learning_paths');
        Schema::dropIfExists('skill_gaps');
        Schema::dropIfExists('user_skills');
        Schema::dropIfExists('resume_keywords');
        Schema::dropIfExists('ai_resume_generations');
        Schema::dropIfExists('resume_analytics');
        Schema::dropIfExists('resume_versions');
        Schema::dropIfExists('resume_ai_suggestions');
        Schema::dropIfExists('resumes');
    }
};
