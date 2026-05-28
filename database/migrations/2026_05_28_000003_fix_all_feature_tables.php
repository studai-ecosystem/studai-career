<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * COMPREHENSIVE FEATURE TABLE REPAIR — 2026-05-28
 *
 * Creates ALL missing feature tables that were recorded as "run" in the
 * migrations table but never actually created in production.
 *
 * Covers: Negotiation, Career Coach, Autonomous Agent, Interview Intelligence,
 *         Gamification, and Talent Marketplace features.
 *
 * Every block is guarded with Schema::hasTable — fully idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════════════════
        // NEGOTIATION STRATEGIST TABLES
        // ═══════════════════════════════════════════════════════════════════

        if (! Schema::hasTable('negotiation_strategies')) {
            Schema::create('negotiation_strategies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('role');
                $table->string('company_name');
                $table->string('location');
                $table->decimal('offered_salary', 12, 2);
                $table->decimal('current_salary', 12, 2)->nullable();
                $table->integer('years_experience');
                $table->decimal('market_median', 12, 2);
                $table->decimal('market_percentile_25', 12, 2);
                $table->decimal('market_percentile_75', 12, 2);
                $table->decimal('market_percentile_90', 12, 2);
                $table->decimal('offered_salary_percentile', 5, 2);
                $table->json('company_salary_data')->nullable();
                $table->decimal('optimal_ask', 12, 2);
                $table->decimal('minimum_acceptable', 12, 2);
                $table->decimal('stretch_goal', 12, 2);
                $table->decimal('confidence_score', 5, 2);
                $table->json('strongest_points');
                $table->json('value_propositions');
                $table->json('risk_factors');
                $table->enum('recommended_timing', ['immediate', 'within_24h', 'within_48h', 'within_week']);
                $table->text('timing_rationale');
                $table->enum('recommended_tone', ['collaborative', 'confident', 'enthusiastic', 'analytical']);
                $table->json('recommended_tactics');
                $table->json('benefits_to_negotiate')->nullable();
                $table->json('total_comp_optimization')->nullable();
                $table->json('company_culture_analysis')->nullable();
                $table->text('hiring_manager_perspective')->nullable();
                $table->enum('company_negotiation_flexibility', ['high', 'medium', 'low', 'unknown'])->default('unknown');
                $table->text('ai_summary');
                $table->text('ai_rationale');
                $table->json('ai_warnings')->nullable();
                $table->decimal('actual_outcome', 12, 2)->nullable();
                $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('active');
                $table->timestamp('generated_at');
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
                $table->index('status');
            });
        }

        if (! Schema::hasTable('negotiation_scenarios')) {
            Schema::create('negotiation_scenarios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('strategy_id')->constrained('negotiation_strategies')->onDelete('cascade');
                $table->string('scenario_name');
                $table->integer('scenario_order')->default(0);
                $table->decimal('counter_offer_amount', 12, 2);
                $table->json('additional_requests')->nullable();
                $table->text('counter_offer_justification');
                $table->enum('predicted_response', ['accept', 'counter', 'negotiate', 'reject']);
                $table->decimal('predicted_response_probability', 5, 2);
                $table->decimal('predicted_final_salary', 12, 2)->nullable();
                $table->text('predicted_employer_counter')->nullable();
                $table->enum('risk_level', ['low', 'medium', 'high', 'very_high']);
                $table->decimal('risk_score', 5, 2);
                $table->json('risk_factors');
                $table->json('mitigation_strategies')->nullable();
                $table->decimal('best_case_outcome', 12, 2);
                $table->decimal('expected_outcome', 12, 2);
                $table->decimal('worst_case_outcome', 12, 2);
                $table->json('success_indicators');
                $table->json('failure_indicators');
                $table->enum('recommendation', ['recommended', 'viable', 'risky', 'not_recommended']);
                $table->text('recommendation_rationale');
                $table->integer('confidence_score')->default(0);
                $table->timestamps();
                $table->index(['strategy_id', 'scenario_order']);
            });
        }

        if (! Schema::hasTable('negotiation_scripts')) {
            Schema::create('negotiation_scripts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('strategy_id')->constrained('negotiation_strategies')->onDelete('cascade');
                $table->unsignedBigInteger('scenario_id')->nullable();
                $table->enum('script_type', ['email', 'phone', 'in_person', 'video_call']);
                $table->enum('script_stage', ['initial_response', 'counter_offer', 'follow_up', 'closing']);
                $table->string('script_name');
                $table->text('subject_line')->nullable();
                $table->text('opening');
                $table->text('body');
                $table->text('closing');
                $table->text('full_script')->nullable();
                $table->json('key_talking_points');
                $table->json('phrases_to_use');
                $table->json('phrases_to_avoid');
                $table->json('transition_phrases');
                $table->enum('tone', ['professional', 'enthusiastic', 'collaborative', 'confident', 'grateful']);
                $table->string('formality_level')->nullable();
                $table->json('cultural_adaptations')->nullable();
                $table->text('personality_notes')->nullable();
                $table->json('anchoring_tactics')->nullable();
                $table->json('framing_strategies')->nullable();
                $table->json('reciprocity_elements')->nullable();
                $table->boolean('includes_deadline')->default(false);
                $table->boolean('includes_alternatives')->default(false);
                $table->boolean('includes_data')->default(false);
                $table->integer('effectiveness_rating')->nullable();
                $table->boolean('was_used')->default(false);
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
                $table->index(['strategy_id', 'script_type', 'script_stage']);
            });
        }

        if (! Schema::hasTable('negotiation_sessions')) {
            Schema::create('negotiation_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('strategy_id')->constrained('negotiation_strategies')->onDelete('cascade');
                $table->unsignedBigInteger('scenario_id')->nullable();
                $table->enum('session_type', ['preparation', 'live_coaching', 'post_mortem']);
                $table->enum('communication_mode', ['email', 'phone', 'in_person', 'video_call']);
                $table->datetime('session_start');
                $table->datetime('session_end')->nullable();
                $table->integer('duration_minutes')->nullable();
                $table->text('session_goal')->nullable();
                $table->enum('current_stage', ['initial_offer', 'counter_offer', 'negotiation', 'closing', 'completed']);
                $table->json('session_context')->nullable();
                $table->json('key_points_discussed')->nullable();
                $table->json('employer_signals')->nullable();
                $table->json('user_performance')->nullable();
                $table->json('ai_interventions')->nullable();
                $table->enum('outcome', ['successful', 'pending', 'needs_follow_up', 'unsuccessful', 'user_withdrew'])->nullable();
                $table->decimal('final_agreed_salary', 12, 2)->nullable();
                $table->json('final_agreed_terms')->nullable();
                $table->text('outcome_notes')->nullable();
                $table->json('what_worked_well')->nullable();
                $table->json('what_to_improve')->nullable();
                $table->json('lessons_learned')->nullable();
                $table->integer('user_satisfaction')->nullable();
                $table->json('skill_scores')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'session_start']);
                $table->index('outcome');
            });
        }

        if (! Schema::hasTable('negotiation_messages')) {
            Schema::create('negotiation_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('session_id')->constrained('negotiation_sessions')->onDelete('cascade');
                $table->enum('message_type', ['user_input', 'employer_response', 'ai_suggestion', 'ai_analysis', 'system_note']);
                $table->text('content');
                $table->json('metadata')->nullable();
                $table->enum('suggestion_category', [
                    'response_suggestion', 'tactic_recommendation', 'warning',
                    'encouragement', 'data_point', 'pivot_suggestion', 'closing_advice'
                ])->nullable();
                $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->nullable();
                $table->integer('confidence_score')->nullable();
                $table->unsignedBigInteger('in_response_to')->nullable();
                $table->json('suggested_responses')->nullable();
                $table->json('context_analysis')->nullable();
                $table->boolean('was_helpful')->nullable();
                $table->boolean('was_used')->default(false);
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
                $table->index(['session_id', 'created_at']);
                $table->index('message_type');
            });
        }

        if (! Schema::hasTable('negotiation_tactics')) {
            Schema::create('negotiation_tactics', function (Blueprint $table) {
                $table->id();
                $table->string('tactic_name');
                $table->string('tactic_category');
                $table->text('description');
                $table->text('when_to_use');
                $table->text('how_to_execute');
                $table->json('example_phrases');
                $table->enum('risk_level', ['low', 'medium', 'high']);
                $table->json('best_for_roles')->nullable();
                $table->json('best_for_industries')->nullable();
                $table->decimal('average_effectiveness', 5, 2)->default(0);
                $table->integer('times_recommended')->default(0);
                $table->integer('times_used')->default(0);
                $table->integer('times_successful')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index('tactic_category');
                $table->index('risk_level');
            });
        }

        // ═══════════════════════════════════════════════════════════════════
        // CAREER COACH TABLES
        // ═══════════════════════════════════════════════════════════════════

        if (! Schema::hasTable('career_coach_sessions')) {
            Schema::create('career_coach_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title')->nullable();
                $table->enum('session_type', [
                    'general_advice', 'career_planning', 'skill_development', 'job_search',
                    'interview_prep', 'salary_negotiation', 'career_transition', 'goal_review', 'weekly_checkin'
                ])->default('general_advice');
                $table->json('context')->nullable();
                $table->json('summary')->nullable();
                $table->json('action_items')->nullable();
                $table->json('key_insights')->nullable();
                $table->integer('message_count')->default(0);
                $table->timestamp('last_message_at')->nullable();
                $table->enum('status', ['active', 'completed', 'archived'])->default('active');
                $table->timestamps();
                $table->softDeletes();
                $table->index(['user_id', 'status']);
                $table->index(['user_id', 'session_type']);
            });
        }

        if (! Schema::hasTable('career_coach_messages')) {
            Schema::create('career_coach_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('session_id')->constrained('career_coach_sessions')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('role', ['user', 'assistant', 'system']);
                $table->text('content');
                $table->json('metadata')->nullable();
                $table->json('voice_data')->nullable();
                $table->boolean('is_voice_input')->default(false);
                $table->boolean('is_voice_output')->default(false);
                $table->string('sentiment')->nullable();
                $table->json('extracted_entities')->nullable();
                $table->integer('tokens_used')->nullable();
                $table->timestamps();
                $table->index(['session_id', 'created_at']);
                $table->index(['user_id', 'role']);
            });
        }

        if (! Schema::hasTable('career_goals')) {
            Schema::create('career_goals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('category', [
                    'role_change', 'salary_increase', 'skill_acquisition', 'certification',
                    'promotion', 'career_pivot', 'side_project', 'networking',
                    'work_life_balance', 'leadership', 'entrepreneurship', 'education', 'other'
                ]);
                $table->enum('timeframe', ['1_month', '3_months', '6_months', '1_year', '2_years', '5_years', 'ongoing'])->default('6_months');
                $table->date('target_date')->nullable();
                $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->enum('status', ['not_started', 'in_progress', 'on_hold', 'completed', 'abandoned'])->default('not_started');
                $table->integer('progress_percentage')->default(0);
                $table->json('milestones')->nullable();
                $table->json('metrics')->nullable();
                $table->json('obstacles')->nullable();
                $table->json('resources')->nullable();
                $table->json('ai_recommendations')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['user_id', 'status']);
                $table->index(['user_id', 'category']);
            });
        }

        if (! Schema::hasTable('career_goal_updates')) {
            Schema::create('career_goal_updates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('goal_id')->constrained('career_goals')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('session_id')->nullable();
                $table->text('update_content');
                $table->integer('progress_before')->nullable();
                $table->integer('progress_after')->nullable();
                $table->json('milestones_completed')->nullable();
                $table->json('challenges_faced')->nullable();
                $table->json('next_steps')->nullable();
                $table->json('ai_feedback')->nullable();
                $table->string('mood')->nullable();
                $table->timestamps();
                $table->index(['goal_id', 'created_at']);
                $table->index('user_id');
            });
        }

        if (! Schema::hasTable('career_coach_checkins')) {
            Schema::create('career_coach_checkins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('session_id')->nullable();
                $table->date('scheduled_for');
                $table->date('completed_at')->nullable();
                $table->enum('status', ['scheduled', 'completed', 'skipped', 'rescheduled'])->default('scheduled');
                $table->json('goals_reviewed')->nullable();
                $table->json('wins_this_week')->nullable();
                $table->json('challenges_this_week')->nullable();
                $table->json('focus_for_next_week')->nullable();
                $table->json('ai_summary')->nullable();
                $table->integer('overall_sentiment_score')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'scheduled_for']);
                $table->index(['user_id', 'status']);
                $table->unique(['user_id', 'scheduled_for']);
            });
        }

        if (! Schema::hasTable('career_coach_suggestions')) {
            Schema::create('career_coach_suggestions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('goal_id')->nullable();
                $table->string('title');
                $table->text('content');
                $table->enum('type', [
                    'skill_recommendation', 'job_opportunity', 'networking_tip',
                    'learning_resource', 'industry_insight', 'motivation',
                    'deadline_reminder', 'goal_nudge', 'celebration'
                ]);
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
                $table->json('action_link')->nullable();
                $table->json('metadata')->nullable();
                $table->boolean('is_read')->default(false);
                $table->boolean('is_dismissed')->default(false);
                $table->boolean('is_acted_upon')->default(false);
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'is_read']);
                $table->index(['user_id', 'type']);
            });
        }

        if (! Schema::hasTable('career_coach_preferences')) {
            Schema::create('career_coach_preferences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('weekly_checkins_enabled')->default(true);
                $table->string('preferred_checkin_day')->default('monday');
                $table->string('preferred_checkin_time')->default('09:00');
                $table->string('timezone')->default('Asia/Kolkata');
                $table->boolean('proactive_suggestions_enabled')->default(true);
                $table->enum('suggestion_frequency', ['daily', 'weekly', 'occasional'])->default('weekly');
                $table->boolean('voice_enabled')->default(false);
                $table->string('preferred_language')->default('en');
                $table->enum('coaching_style', ['supportive', 'direct', 'analytical', 'motivational'])->default('supportive');
                $table->json('focus_areas')->nullable();
                $table->boolean('email_notifications')->default(true);
                $table->boolean('push_notifications')->default(true);
                $table->timestamps();
                $table->unique('user_id');
            });
        }

        // ═══════════════════════════════════════════════════════════════════
        // AUTONOMOUS AGENT TABLES
        // ═══════════════════════════════════════════════════════════════════

        if (! Schema::hasTable('agent_configurations')) {
            Schema::create('agent_configurations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('is_active')->default(false);
                $table->boolean('is_paused')->default(false);
                $table->integer('daily_application_limit')->default(5);
                $table->integer('applications_this_month')->default(0);
                $table->integer('applications_today')->default(0);
                $table->date('applications_today_date')->nullable();
                $table->json('target_roles')->nullable();
                $table->json('preferred_locations')->nullable();
                $table->json('required_skills')->nullable();
                $table->json('nice_to_have_skills')->nullable();
                $table->integer('min_salary')->nullable();
                $table->integer('max_salary')->nullable();
                $table->enum('salary_period', ['hourly', 'monthly', 'yearly'])->default('yearly');
                $table->json('company_sizes')->nullable();
                $table->json('work_arrangements')->nullable();
                $table->json('employment_types')->nullable();
                $table->integer('min_experience_years')->nullable();
                $table->integer('max_experience_years')->nullable();
                $table->json('industries')->nullable();
                $table->json('excluded_keywords')->nullable();
                $table->boolean('only_verified_companies')->default(false);
                $table->boolean('require_visa_sponsorship')->default(false);
                $table->enum('application_aggressiveness', ['conservative', 'moderate', 'aggressive'])->default('moderate');
                $table->integer('match_threshold_percentage')->default(70);
                $table->boolean('auto_follow_up')->default(true);
                $table->boolean('require_approval')->default(false);
                $table->integer('approval_threshold')->default(80);
                $table->integer('follow_up_days')->default(7);
                $table->boolean('enable_learning')->default(true);
                $table->json('learning_metrics')->nullable();
                $table->timestamp('last_optimization_at')->nullable();
                $table->json('active_hours')->nullable();
                $table->json('active_days')->nullable();
                $table->timestamp('next_run_at')->nullable();
                $table->timestamp('last_run_at')->nullable();
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('deactivated_at')->nullable();
                $table->timestamp('paused_at')->nullable();
                $table->string('pause_reason')->nullable();
                $table->timestamp('emergency_stopped_at')->nullable();
                $table->unsignedBigInteger('emergency_stopped_by')->nullable();
                $table->text('emergency_stop_reason')->nullable();
                $table->boolean('is_globally_stopped')->default(false);
                $table->timestamps();
                $table->softDeletes();
                $table->unique('user_id');
                $table->index(['is_active', 'next_run_at']);
                $table->index('emergency_stopped_at');
                $table->index('is_globally_stopped');
            });
        }

        if (! Schema::hasTable('job_sources')) {
            Schema::create('job_sources', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type');
                $table->string('url')->nullable();
                $table->json('scraping_config')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('priority')->default(5);
                $table->integer('success_rate')->default(0);
                $table->timestamp('last_scraped_at')->nullable();
                $table->integer('jobs_found_today')->default(0);
                $table->integer('jobs_found_total')->default(0);
                $table->timestamps();
                $table->index(['is_active', 'priority']);
            });
        }

        if (! Schema::hasTable('discovered_jobs')) {
            Schema::create('discovered_jobs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_source_id')->constrained()->onDelete('cascade');
                $table->string('external_id')->nullable();
                $table->string('url')->unique();
                $table->string('title');
                $table->string('company_name');
                $table->text('description');
                $table->text('requirements')->nullable();
                $table->json('extracted_skills')->nullable();
                $table->string('location')->nullable();
                $table->boolean('is_remote')->default(false);
                $table->string('work_arrangement')->nullable();
                $table->integer('salary_min')->nullable();
                $table->integer('salary_max')->nullable();
                $table->string('salary_period')->nullable();
                $table->string('salary_currency')->default('USD');
                $table->string('employment_type')->nullable();
                $table->string('experience_level')->nullable();
                $table->integer('applicant_count')->nullable();
                $table->timestamp('posted_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_processed')->default(false);
                $table->boolean('is_duplicate')->default(false);
                $table->unsignedBigInteger('duplicate_of_id')->nullable();
                $table->json('matched_user_ids')->nullable();
                $table->float('ats_score')->nullable();
                $table->timestamps();
                $table->index(['job_source_id', 'is_processed']);
                $table->index('posted_at');
            });
        }

        if (! Schema::hasTable('job_matches')) {
            Schema::create('job_matches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('discovered_job_id')->constrained()->onDelete('cascade');
                $table->float('overall_match_score');
                $table->json('score_breakdown')->nullable();
                $table->json('matching_skills')->nullable();
                $table->json('missing_skills')->nullable();
                $table->enum('agent_decision', ['apply', 'review', 'skip'])->default('review');
                $table->text('decision_reasoning')->nullable();
                $table->float('confidence_score')->nullable();
                $table->enum('user_override', ['approve', 'reject', 'pending'])->nullable();
                $table->text('user_notes')->nullable();
                $table->boolean('has_applied')->default(false);
                $table->timestamp('applied_at')->nullable();
                $table->unsignedBigInteger('auto_application_id')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'discovered_job_id']);
                $table->index(['user_id', 'agent_decision']);
            });
        }

        if (! Schema::hasTable('auto_applications')) {
            Schema::create('auto_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('job_match_id')->constrained()->onDelete('cascade');
                $table->foreignId('discovered_job_id')->constrained();
                $table->text('customized_resume_path')->nullable();
                $table->text('customized_resume_content')->nullable();
                $table->text('cover_letter')->nullable();
                $table->json('screening_answers')->nullable();
                $table->json('custom_fields')->nullable();
                $table->json('resume_changes')->nullable();
                $table->json('keywords_optimized')->nullable();
                $table->float('ats_optimization_score')->nullable();
                $table->enum('submission_method', ['api', 'email', 'form', 'manual_review'])->default('manual_review');
                $table->enum('status', ['pending', 'submitted', 'failed', 'requires_manual'])->default('pending');
                $table->text('submission_response')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->enum('application_status', [
                    'submitted', 'viewed', 'screening', 'interviewing',
                    'offered', 'rejected', 'withdrawn', 'ghosted'
                ])->default('submitted');
                $table->timestamp('status_updated_at')->nullable();
                $table->json('status_history')->nullable();
                $table->boolean('follow_up_sent')->default(false);
                $table->timestamp('follow_up_at')->nullable();
                $table->integer('follow_up_count')->default(0);
                $table->boolean('got_response')->default(false);
                $table->boolean('got_interview')->default(false);
                $table->boolean('got_offer')->default(false);
                $table->text('rejection_reason')->nullable();
                $table->json('feedback')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['user_id', 'status']);
                $table->index(['application_status', 'submitted_at']);
            });
        }

        if (! Schema::hasTable('application_activity_logs')) {
            Schema::create('application_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('auto_application_id')->nullable();
                $table->unsignedBigInteger('discovered_job_id')->nullable();
                $table->string('action_type');
                $table->text('description');
                $table->json('metadata')->nullable();
                $table->enum('severity', ['info', 'success', 'warning', 'error'])->default('info');
                $table->timestamp('created_at');
                $table->index(['user_id', 'created_at']);
                $table->index('action_type');
            });
        }

        if (! Schema::hasTable('agent_learning_metrics')) {
            Schema::create('agent_learning_metrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->json('successful_job_patterns')->nullable();
                $table->json('unsuccessful_job_patterns')->nullable();
                $table->json('keyword_performance')->nullable();
                $table->json('company_type_performance')->nullable();
                $table->float('average_match_score_applied')->nullable();
                $table->float('average_response_rate')->nullable();
                $table->float('average_interview_rate')->nullable();
                $table->json('best_application_times')->nullable();
                $table->json('resume_optimization_effectiveness')->nullable();
                $table->json('cover_letter_templates_performance')->nullable();
                $table->integer('total_applications')->default(0);
                $table->integer('total_responses')->default(0);
                $table->integer('total_interviews')->default(0);
                $table->integer('total_offers')->default(0);
                $table->timestamp('last_learning_cycle_at')->nullable();
                $table->timestamps();
                $table->unique('user_id');
            });
        }

        if (! Schema::hasTable('application_templates')) {
            Schema::create('application_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->enum('type', ['cover_letter', 'resume', 'screening_answers']);
                $table->text('content');
                $table->json('variables')->nullable();
                $table->json('target_roles')->nullable();
                $table->integer('times_used')->default(0);
                $table->float('success_rate')->nullable();
                $table->float('average_match_score')->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();
                $table->index(['user_id', 'type']);
            });
        }

        if (! Schema::hasTable('agent_audit_logs')) {
            Schema::create('agent_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('action');
                $table->string('entity_type')->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->json('before')->nullable();
                $table->json('after')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
                $table->index(['entity_type', 'entity_id']);
            });
        }

        // ═══════════════════════════════════════════════════════════════════
        // INTERVIEW INTELLIGENCE TABLES
        // ═══════════════════════════════════════════════════════════════════

        if (! Schema::hasTable('company_interview_data')) {
            Schema::create('company_interview_data', function (Blueprint $table) {
                $table->id();
                $table->string('company_name')->index();
                $table->string('role_title')->nullable();
                $table->string('department')->nullable();
                $table->string('interview_type')->nullable();
                $table->json('common_questions');
                $table->json('interviewer_profiles')->nullable();
                $table->json('interview_structure')->nullable();
                $table->json('difficulty_ratings')->nullable();
                $table->json('success_patterns')->nullable();
                $table->json('cultural_values')->nullable();
                $table->json('technical_focus_areas')->nullable();
                $table->text('notes')->nullable();
                $table->integer('data_points_count')->default(0);
                $table->timestamp('last_updated_at')->nullable();
                $table->timestamps();
                $table->index(['company_name', 'role_title']);
            });
        }

        if (! Schema::hasTable('interview_sessions')) {
            Schema::create('interview_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('cache_key')->nullable()->unique();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('discovered_job_id')->nullable();
                $table->string('company_name')->nullable();
                $table->string('role_title')->nullable();
                $table->string('job_title')->nullable();
                $table->string('interview_type');
                $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
                $table->integer('total_questions')->default(0);
                $table->integer('questions_answered')->default(0);
                $table->integer('duration_minutes')->nullable();
                $table->decimal('overall_score', 5, 2)->nullable();
                $table->json('performance_metrics')->nullable();
                $table->json('ai_insights')->nullable();
                $table->json('session_data')->nullable();
                $table->json('interviewer_style')->nullable();
                $table->json('skill_map')->nullable();
                $table->string('focus_skill')->nullable();
                $table->decimal('vantage_score', 3, 2)->nullable();
                $table->timestamp('evaluator_ran_at')->nullable();
                $table->timestamp('started_at');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['user_id', 'status']);
                $table->index(['company_name', 'role_title']);
            });
        }

        if (! Schema::hasTable('interview_questions')) {
            Schema::create('interview_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('interview_session_id')->constrained()->onDelete('cascade');
                $table->integer('question_order')->default(1);
                $table->string('question_type');
                $table->text('question_text');
                $table->json('question_context')->nullable();
                $table->string('difficulty_level')->nullable();
                $table->json('expected_elements')->nullable();
                $table->json('star_components')->nullable();
                $table->text('ideal_answer_outline')->nullable();
                $table->json('follow_up_questions')->nullable();
                $table->json('interviewer_notes')->nullable();
                $table->boolean('is_company_specific')->default(false);
                $table->text('company_context')->nullable();
                $table->timestamps();
                $table->index(['interview_session_id', 'question_order']);
            });
        }

        if (! Schema::hasTable('interview_responses')) {
            Schema::create('interview_responses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('interview_question_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('response_type', ['text', 'voice', 'video'])->default('text');
                $table->text('response_text')->nullable();
                $table->string('audio_file_path')->nullable();
                $table->string('video_file_path')->nullable();
                $table->text('transcription')->nullable();
                $table->integer('response_time_seconds')->nullable();
                $table->integer('word_count')->nullable();
                $table->decimal('confidence_score', 5, 2)->nullable();
                $table->decimal('clarity_score', 5, 2)->nullable();
                $table->decimal('structure_score', 5, 2)->nullable();
                $table->decimal('content_score', 5, 2)->nullable();
                $table->decimal('overall_score', 5, 2)->nullable();
                $table->json('filler_words')->nullable();
                $table->json('star_analysis')->nullable();
                $table->json('keywords_used')->nullable();
                $table->json('missing_elements')->nullable();
                $table->timestamp('answered_at');
                $table->timestamps();
                $table->index(['user_id', 'answered_at']);
            });
        }

        if (! Schema::hasTable('interview_feedback')) {
            Schema::create('interview_feedback', function (Blueprint $table) {
                $table->id();
                $table->foreignId('interview_response_id')->constrained()->onDelete('cascade');
                $table->enum('feedback_type', ['real_time', 'post_response', 'session_summary']);
                $table->text('feedback_text');
                $table->json('strengths')->nullable();
                $table->json('improvements')->nullable();
                $table->json('suggestions')->nullable();
                $table->json('example_answers')->nullable();
                $table->boolean('is_positive')->default(true);
                $table->string('focus_area')->nullable();
                $table->integer('priority')->default(5);
                $table->timestamps();
                $table->index(['interview_response_id', 'feedback_type']);
            });
        }

        if (! Schema::hasTable('interview_performance_reports')) {
            Schema::create('interview_performance_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('interview_session_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('overall_score', 5, 2);
                $table->json('category_scores');
                $table->json('strengths');
                $table->json('weaknesses');
                $table->json('filler_word_analysis');
                $table->json('star_methodology_score')->nullable();
                $table->json('company_fit_analysis')->nullable();
                $table->json('actionable_improvements');
                $table->json('recommended_practice_areas');
                $table->json('comparison_metrics')->nullable();
                $table->text('executive_summary');
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('interview_coaching_tips')) {
            Schema::create('interview_coaching_tips', function (Blueprint $table) {
                $table->id();
                $table->foreignId('interview_session_id')->constrained()->onDelete('cascade');
                $table->string('company_name');
                $table->string('role_title');
                $table->json('company_talking_points');
                $table->json('role_specific_tips');
                $table->json('interviewer_insights')->nullable();
                $table->json('cultural_alignment_points');
                $table->json('technical_prep_areas')->nullable();
                $table->json('common_mistakes')->nullable();
                $table->json('success_strategies');
                $table->timestamps();
            });
        }

        // ═══════════════════════════════════════════════════════════════════
        // GAMIFICATION TABLES
        // ═══════════════════════════════════════════════════════════════════

        if (! Schema::hasTable('user_gamification_profiles')) {
            Schema::create('user_gamification_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('total_points')->default(0);
                $table->unsignedBigInteger('available_points')->default(0);
                $table->unsignedInteger('level')->default(1);
                $table->unsignedBigInteger('xp_current')->default(0);
                $table->unsignedBigInteger('xp_required')->default(100);
                $table->unsignedInteger('current_streak')->default(0);
                $table->unsignedInteger('longest_streak')->default(0);
                $table->date('last_activity_date')->nullable();
                $table->unsignedInteger('streak_freeze_count')->default(0);
                $table->unsignedInteger('achievements_unlocked')->default(0);
                $table->unsignedInteger('badges_earned')->default(0);
                $table->unsignedInteger('challenges_completed')->default(0);
                $table->unsignedInteger('rewards_redeemed')->default(0);
                $table->boolean('show_on_leaderboard')->default(true);
                $table->string('leaderboard_display_name')->nullable();
                $table->string('primary_industry')->nullable();
                $table->decimal('xp_multiplier', 4, 2)->default(1.00);
                $table->timestamp('multiplier_expires_at')->nullable();
                $table->timestamps();
                $table->unique('user_id');
                $table->index(['total_points', 'level']);
            });
        }

        if (! Schema::hasTable('achievements')) {
            Schema::create('achievements', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->text('description');
                $table->string('category');
                $table->string('icon')->nullable();
                $table->string('tier')->default('bronze');
                $table->string('trigger_type');
                $table->string('trigger_action');
                $table->unsignedInteger('trigger_count')->default(1);
                $table->json('trigger_conditions')->nullable();
                $table->unsignedInteger('points_reward')->default(0);
                $table->unsignedInteger('xp_reward')->default(0);
                $table->string('badge_reward')->nullable();
                $table->boolean('is_secret')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['category', 'tier']);
                $table->index('trigger_action');
            });
        }

        if (! Schema::hasTable('user_achievements')) {
            Schema::create('user_achievements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
                $table->timestamp('unlocked_at');
                $table->unsignedInteger('progress')->default(0);
                $table->unsignedInteger('target')->default(1);
                $table->boolean('is_completed')->default(false);
                $table->boolean('is_claimed')->default(false);
                $table->timestamp('claimed_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'achievement_id']);
                $table->index(['user_id', 'is_completed']);
            });
        }

        if (! Schema::hasTable('gamification_badges')) {
            Schema::create('gamification_badges', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->text('description');
                $table->string('category');
                $table->string('icon');
                $table->string('color')->default('#6366f1');
                $table->string('rarity')->default('common');
                $table->string('earn_type');
                $table->unsignedBigInteger('earn_reference_id')->nullable();
                $table->unsignedInteger('purchase_cost')->nullable();
                $table->boolean('is_displayable')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamp('available_from')->nullable();
                $table->timestamp('available_until')->nullable();
                $table->timestamps();
                $table->index(['category', 'rarity']);
            });
        }

        if (! Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('badge_id')->constrained('gamification_badges')->onDelete('cascade');
                $table->timestamp('earned_at');
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('display_order')->default(0);
                $table->timestamps();
                $table->unique(['user_id', 'badge_id']);
                $table->index(['user_id', 'is_featured']);
            });
        }

        if (! Schema::hasTable('daily_challenges')) {
            Schema::create('daily_challenges', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->text('description');
                $table->string('category');
                $table->string('difficulty')->default('easy');
                $table->string('action_type');
                $table->unsignedInteger('action_count')->default(1);
                $table->json('action_conditions')->nullable();
                $table->unsignedInteger('points_reward')->default(10);
                $table->unsignedInteger('xp_reward')->default(25);
                $table->unsignedInteger('streak_bonus')->default(0);
                $table->string('day_of_week')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('weight')->default(1);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_daily_challenges')) {
            Schema::create('user_daily_challenges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('challenge_id')->constrained('daily_challenges')->onDelete('cascade');
                $table->date('challenge_date');
                $table->unsignedInteger('progress')->default(0);
                $table->unsignedInteger('target')->default(1);
                $table->boolean('is_completed')->default(false);
                $table->boolean('is_claimed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('claimed_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'challenge_id', 'challenge_date']);
                $table->index(['user_id', 'challenge_date']);
            });
        }

        if (! Schema::hasTable('leaderboards')) {
            Schema::create('leaderboards', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('type');
                $table->string('industry')->nullable();
                $table->string('metric');
                $table->date('period_start')->nullable();
                $table->date('period_end')->nullable();
                $table->json('rank_rewards')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['type', 'is_active']);
            });
        }

        if (! Schema::hasTable('leaderboard_entries')) {
            Schema::create('leaderboard_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('leaderboard_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedInteger('rank')->default(0);
                $table->unsignedBigInteger('score')->default(0);
                $table->unsignedInteger('previous_rank')->nullable();
                $table->integer('rank_change')->default(0);
                $table->timestamps();
                $table->unique(['leaderboard_id', 'user_id']);
                $table->index(['leaderboard_id', 'rank']);
            });
        }

        if (! Schema::hasTable('rewards')) {
            Schema::create('rewards', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->text('description');
                $table->string('category');
                $table->string('type');
                $table->unsignedInteger('points_cost')->default(0);
                $table->unsignedInteger('level_required')->default(1);
                $table->string('reward_type');
                $table->json('reward_data')->nullable();
                $table->unsignedInteger('duration_days')->nullable();
                $table->unsignedInteger('stock')->nullable();
                $table->unsignedInteger('redeemed_count')->default(0);
                $table->unsignedInteger('per_user_limit')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamp('available_from')->nullable();
                $table->timestamp('available_until')->nullable();
                $table->timestamps();
                $table->index(['category', 'is_active']);
            });
        }

        if (! Schema::hasTable('user_rewards')) {
            Schema::create('user_rewards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('reward_id')->constrained()->onDelete('cascade');
                $table->unsignedInteger('points_spent')->default(0);
                $table->string('status')->default('active');
                $table->timestamp('redeemed_at');
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
            });
        }

        if (! Schema::hasTable('points_transactions')) {
            Schema::create('points_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type');
                $table->integer('points');
                $table->unsignedBigInteger('balance_after')->default(0);
                $table->string('source');
                $table->string('source_type')->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('xp_transactions')) {
            Schema::create('xp_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedInteger('xp_earned')->default(0);
                $table->unsignedInteger('level_before');
                $table->unsignedInteger('level_after');
                $table->boolean('leveled_up')->default(false);
                $table->string('source');
                $table->string('source_type')->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->decimal('multiplier_applied', 4, 2)->default(1.00);
                $table->text('description')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('seasonal_events')) {
            Schema::create('seasonal_events', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->text('description');
                $table->string('theme');
                $table->timestamp('starts_at');
                $table->timestamp('ends_at');
                $table->decimal('xp_multiplier', 4, 2)->default(1.00);
                $table->decimal('points_multiplier', 4, 2)->default(1.00);
                $table->json('exclusive_badges')->nullable();
                $table->json('exclusive_challenges')->nullable();
                $table->json('exclusive_rewards')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['starts_at', 'ends_at']);
            });
        }

        if (! Schema::hasTable('user_event_participation')) {
            Schema::create('user_event_participation', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('event_id')->constrained('seasonal_events')->onDelete('cascade');
                $table->unsignedInteger('event_points')->default(0);
                $table->unsignedInteger('event_xp')->default(0);
                $table->unsignedInteger('tasks_completed')->default(0);
                $table->json('rewards_claimed')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'event_id']);
            });
        }

        if (! Schema::hasTable('gamification_activities')) {
            Schema::create('gamification_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('action');
                $table->string('action_category');
                $table->string('actionable_type')->nullable();
                $table->unsignedBigInteger('actionable_id')->nullable();
                $table->unsignedInteger('points_earned')->default(0);
                $table->unsignedInteger('xp_earned')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'action', 'created_at']);
                $table->index(['action', 'created_at']);
            });
        }

        if (! Schema::hasTable('gamification_referral_bonuses')) {
            Schema::create('gamification_referral_bonuses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
                $table->string('milestone');
                $table->unsignedInteger('points_awarded')->default(0);
                $table->unsignedInteger('xp_awarded')->default(0);
                $table->boolean('is_claimed')->default(false);
                $table->timestamp('claimed_at')->nullable();
                $table->timestamps();
                $table->unique(['referrer_id', 'referred_id', 'milestone'], 'gam_ref_bonus_unique');
            });
        }

        // ═══════════════════════════════════════════════════════════════════
        // TALENT MARKETPLACE TABLES
        // ═══════════════════════════════════════════════════════════════════

        if (! Schema::hasTable('marketplace_projects')) {
            Schema::create('marketplace_projects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description');
                $table->text('requirements')->nullable();
                $table->text('deliverables')->nullable();
                $table->enum('project_type', ['fixed_price', 'hourly', 'milestone'])->default('fixed_price');
                $table->enum('category', [
                    'web_development', 'mobile_development', 'design', 'writing',
                    'marketing', 'data_science', 'ai_ml', 'devops', 'consulting',
                    'video_production', 'audio_production', 'translation', 'legal',
                    'finance', 'admin_support', 'customer_service', 'other'
                ])->default('other');
                $table->json('skills_required')->nullable();
                $table->decimal('budget_min', 12, 2)->nullable();
                $table->decimal('budget_max', 12, 2)->nullable();
                $table->decimal('hourly_rate_min', 10, 2)->nullable();
                $table->decimal('hourly_rate_max', 10, 2)->nullable();
                $table->string('currency', 10)->default('INR');
                $table->enum('experience_level', ['entry', 'intermediate', 'expert'])->default('intermediate');
                $table->integer('estimated_duration_days')->nullable();
                $table->enum('duration_type', ['days', 'weeks', 'months'])->default('weeks');
                $table->enum('status', ['draft', 'open', 'in_progress', 'completed', 'cancelled', 'disputed'])->default('draft');
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_urgent')->default(false);
                $table->boolean('allows_remote')->default(true);
                $table->string('location')->nullable();
                $table->integer('proposals_count')->default(0);
                $table->integer('views_count')->default(0);
                $table->timestamp('deadline')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['status', 'category']);
                $table->index(['employer_id', 'status']);
            });
        }

        if (! Schema::hasTable('freelancer_profiles')) {
            Schema::create('freelancer_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('professional_title');
                $table->text('bio');
                $table->text('overview')->nullable();
                $table->decimal('hourly_rate', 10, 2)->nullable();
                $table->string('currency', 10)->default('INR');
                $table->json('skills')->nullable();
                $table->json('languages')->nullable();
                $table->enum('experience_level', ['entry', 'intermediate', 'expert'])->default('intermediate');
                $table->enum('availability', ['full_time', 'part_time', 'hourly', 'not_available'])->default('full_time');
                $table->integer('hours_per_week')->nullable();
                $table->boolean('available_for_remote')->default(true);
                $table->boolean('available_for_onsite')->default(false);
                $table->string('preferred_project_size')->nullable();
                $table->decimal('total_earnings', 14, 2)->default(0);
                $table->integer('completed_projects')->default(0);
                $table->integer('ongoing_projects')->default(0);
                $table->decimal('success_rate', 5, 2)->default(100);
                $table->decimal('average_rating', 3, 2)->default(0);
                $table->integer('total_reviews')->default(0);
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->timestamp('verified_at')->nullable();
                $table->json('portfolio')->nullable();
                $table->json('certifications')->nullable();
                $table->timestamps();
                $table->unique('user_id');
                $table->index(['is_verified', 'average_rating']);
            });
        }

        if (! Schema::hasTable('marketplace_proposals')) {
            Schema::create('marketplace_proposals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('marketplace_projects')->cascadeOnDelete();
                $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
                $table->text('cover_letter');
                $table->decimal('proposed_amount', 12, 2);
                $table->decimal('hourly_rate', 10, 2)->nullable();
                $table->string('currency', 10)->default('INR');
                $table->integer('estimated_duration_days')->nullable();
                $table->json('milestones')->nullable();
                $table->text('relevant_experience')->nullable();
                $table->json('attachments')->nullable();
                $table->enum('status', ['pending', 'shortlisted', 'accepted', 'rejected', 'withdrawn'])->default('pending');
                $table->unsignedTinyInteger('ai_match_score')->nullable();
                $table->json('ai_match_breakdown')->nullable();
                $table->boolean('is_boosted')->default(false);
                $table->timestamp('boosted_at')->nullable();
                $table->timestamp('viewed_at')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->timestamp('offer_sent_at')->nullable();
                $table->timestamp('offer_responded_at')->nullable();
                $table->decimal('ai_score', 5, 2)->nullable();
                $table->json('ai_feedback')->nullable();
                $table->timestamps();
                $table->unique(['project_id', 'freelancer_id']);
                $table->index(['project_id', 'status']);
                $table->index(['freelancer_id', 'status']);
            });
        }

        if (! Schema::hasTable('marketplace_contracts')) {
            Schema::create('marketplace_contracts', function (Blueprint $table) {
                $table->id();
                $table->string('contract_number')->unique();
                $table->foreignId('project_id')->constrained('marketplace_projects')->cascadeOnDelete();
                $table->foreignId('proposal_id')->constrained('marketplace_proposals')->cascadeOnDelete();
                $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
                $table->text('terms');
                $table->decimal('total_amount', 14, 2);
                $table->decimal('platform_fee_percent', 5, 2)->default(10);
                $table->decimal('platform_fee_amount', 12, 2)->default(0);
                $table->decimal('freelancer_amount', 14, 2);
                $table->string('currency', 10)->default('INR');
                $table->enum('payment_type', ['fixed', 'hourly', 'milestone'])->default('fixed');
                $table->enum('status', ['pending', 'active', 'paused', 'completed', 'cancelled', 'disputed'])->default('pending');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('deadline')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->timestamps();
                $table->index(['employer_id', 'status']);
                $table->index(['freelancer_id', 'status']);
            });
        }

        if (! Schema::hasTable('marketplace_milestones')) {
            Schema::create('marketplace_milestones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contract_id')->constrained('marketplace_contracts')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('deliverables')->nullable();
                $table->decimal('amount', 12, 2);
                $table->string('currency', 10)->default('INR');
                $table->integer('order')->default(0);
                $table->enum('status', ['pending', 'funded', 'in_progress', 'submitted', 'revision_requested', 'approved', 'released', 'disputed'])->default('pending');
                $table->timestamp('due_date')->nullable();
                $table->timestamp('funded_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('released_at')->nullable();
                $table->text('submission_note')->nullable();
                $table->json('submission_files')->nullable();
                $table->text('revision_note')->nullable();
                $table->integer('revision_count')->default(0);
                $table->timestamps();
                $table->index(['contract_id', 'status']);
            });
        }

        if (! Schema::hasTable('marketplace_escrow')) {
            Schema::create('marketplace_escrow', function (Blueprint $table) {
                $table->id();
                $table->string('escrow_id')->unique();
                $table->foreignId('contract_id')->constrained('marketplace_contracts')->cascadeOnDelete();
                $table->unsignedBigInteger('milestone_id')->nullable();
                $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('payee_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('amount', 14, 2);
                $table->decimal('platform_fee', 12, 2)->default(0);
                $table->decimal('net_amount', 14, 2);
                $table->string('currency', 10)->default('INR');
                $table->enum('status', ['pending', 'funded', 'held', 'released', 'refunded', 'disputed'])->default('pending');
                $table->string('payment_gateway')->nullable();
                $table->string('payment_transaction_id')->nullable();
                $table->string('payout_transaction_id')->nullable();
                $table->timestamp('funded_at')->nullable();
                $table->timestamp('held_at')->nullable();
                $table->timestamp('released_at')->nullable();
                $table->timestamp('refunded_at')->nullable();
                $table->text('release_note')->nullable();
                $table->text('refund_reason')->nullable();
                $table->timestamps();
                $table->index(['contract_id', 'status']);
            });
        }

        if (! Schema::hasTable('skill_badges')) {
            Schema::create('skill_badges', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('color', 20)->default('#4F46E5');
                $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
                $table->enum('category', ['technical', 'soft_skill', 'certification', 'platform', 'achievement'])->default('technical');
                $table->json('requirements')->nullable();
                $table->boolean('requires_assessment')->default(false);
                $table->boolean('requires_verification')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_skill_badges')) {
            Schema::create('user_skill_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('badge_id')->constrained('skill_badges')->cascadeOnDelete();
                $table->enum('status', ['pending', 'verified', 'expired', 'revoked'])->default('pending');
                $table->text('verification_evidence')->nullable();
                $table->string('verified_by')->nullable();
                $table->integer('assessment_score')->nullable();
                $table->timestamp('earned_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'badge_id']);
                $table->index(['user_id', 'status']);
            });
        }

        if (! Schema::hasTable('marketplace_reviews')) {
            Schema::create('marketplace_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contract_id')->constrained('marketplace_contracts')->cascadeOnDelete();
                $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
                $table->enum('reviewer_type', ['employer', 'freelancer']);
                $table->tinyInteger('overall_rating');
                $table->tinyInteger('communication_rating')->nullable();
                $table->tinyInteger('quality_rating')->nullable();
                $table->tinyInteger('timeliness_rating')->nullable();
                $table->tinyInteger('professionalism_rating')->nullable();
                $table->tinyInteger('value_rating')->nullable();
                $table->tinyInteger('cooperation_rating')->nullable();
                $table->text('review_text');
                $table->text('private_feedback')->nullable();
                $table->boolean('would_recommend')->default(true);
                $table->boolean('would_hire_again')->nullable();
                $table->json('skills_endorsed')->nullable();
                $table->enum('status', ['pending', 'published', 'hidden', 'disputed'])->default('pending');
                $table->text('employer_response')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->timestamps();
                $table->unique(['contract_id', 'reviewer_id']);
                $table->index(['reviewee_id', 'status']);
            });
        }

        if (! Schema::hasTable('marketplace_messages')) {
            Schema::create('marketplace_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contract_id')->nullable();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
                $table->string('subject')->nullable();
                $table->text('message');
                $table->json('attachments')->nullable();
                $table->enum('message_type', ['inquiry', 'proposal_discussion', 'contract', 'general'])->default('general');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['sender_id', 'recipient_id']);
            });
        }

        if (! Schema::hasTable('marketplace_invitations')) {
            Schema::create('marketplace_invitations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('marketplace_projects')->cascadeOnDelete();
                $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
                $table->text('message')->nullable();
                $table->enum('status', ['pending', 'viewed', 'accepted', 'declined', 'expired'])->default('pending');
                $table->timestamp('viewed_at')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->unique(['project_id', 'freelancer_id']);
                $table->index(['freelancer_id', 'status']);
            });
        }

        if (! Schema::hasTable('marketplace_time_logs')) {
            Schema::create('marketplace_time_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contract_id')->constrained('marketplace_contracts')->cascadeOnDelete();
                $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
                $table->date('work_date');
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->integer('minutes_worked');
                $table->decimal('hourly_rate', 10, 2);
                $table->decimal('amount_earned', 12, 2);
                $table->text('description');
                $table->json('screenshots')->nullable();
                $table->enum('status', ['pending', 'approved', 'disputed', 'paid'])->default('pending');
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
                $table->index(['contract_id', 'work_date']);
            });
        }

        if (! Schema::hasTable('marketplace_disputes')) {
            Schema::create('marketplace_disputes', function (Blueprint $table) {
                $table->id();
                $table->string('dispute_number')->unique();
                $table->foreignId('contract_id')->constrained('marketplace_contracts')->cascadeOnDelete();
                $table->unsignedBigInteger('milestone_id')->nullable();
                $table->foreignId('raised_by_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('against_id')->constrained('users')->cascadeOnDelete();
                $table->enum('dispute_type', ['payment', 'quality', 'deadline', 'scope', 'communication', 'other'])->default('other');
                $table->text('description');
                $table->json('evidence')->nullable();
                $table->decimal('disputed_amount', 14, 2)->nullable();
                $table->enum('status', ['open', 'under_review', 'mediation', 'resolved', 'escalated', 'closed'])->default('open');
                $table->enum('resolution', ['refund_full', 'refund_partial', 'release_full', 'release_partial', 'split', 'dismissed'])->nullable();
                $table->decimal('resolution_amount', 14, 2)->nullable();
                $table->text('resolution_notes')->nullable();
                $table->unsignedBigInteger('resolved_by')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
                $table->index(['contract_id', 'status']);
            });
        }

        if (! Schema::hasTable('saved_freelancers')) {
            Schema::create('saved_freelancers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['employer_id', 'freelancer_id']);
            });
        }

        if (! Schema::hasTable('saved_projects')) {
            Schema::create('saved_projects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('project_id')->constrained('marketplace_projects')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['freelancer_id', 'project_id']);
            });
        }
    }

    public function down(): void
    {
        // Marketplace
        Schema::dropIfExists('saved_projects');
        Schema::dropIfExists('saved_freelancers');
        Schema::dropIfExists('marketplace_disputes');
        Schema::dropIfExists('marketplace_time_logs');
        Schema::dropIfExists('marketplace_invitations');
        Schema::dropIfExists('marketplace_messages');
        Schema::dropIfExists('marketplace_reviews');
        Schema::dropIfExists('user_skill_badges');
        Schema::dropIfExists('skill_badges');
        Schema::dropIfExists('marketplace_escrow');
        Schema::dropIfExists('marketplace_milestones');
        Schema::dropIfExists('marketplace_contracts');
        Schema::dropIfExists('marketplace_proposals');
        Schema::dropIfExists('freelancer_profiles');
        Schema::dropIfExists('marketplace_projects');
        // Gamification
        Schema::dropIfExists('gamification_referral_bonuses');
        Schema::dropIfExists('gamification_activities');
        Schema::dropIfExists('user_event_participation');
        Schema::dropIfExists('seasonal_events');
        Schema::dropIfExists('xp_transactions');
        Schema::dropIfExists('points_transactions');
        Schema::dropIfExists('user_rewards');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('leaderboard_entries');
        Schema::dropIfExists('leaderboards');
        Schema::dropIfExists('user_daily_challenges');
        Schema::dropIfExists('daily_challenges');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('gamification_badges');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('user_gamification_profiles');
        // Interview
        Schema::dropIfExists('interview_coaching_tips');
        Schema::dropIfExists('interview_performance_reports');
        Schema::dropIfExists('interview_feedback');
        Schema::dropIfExists('interview_responses');
        Schema::dropIfExists('interview_questions');
        Schema::dropIfExists('interview_sessions');
        Schema::dropIfExists('company_interview_data');
        // Agent
        Schema::dropIfExists('application_templates');
        Schema::dropIfExists('agent_learning_metrics');
        Schema::dropIfExists('application_activity_logs');
        Schema::dropIfExists('auto_applications');
        Schema::dropIfExists('job_matches');
        Schema::dropIfExists('discovered_jobs');
        Schema::dropIfExists('job_sources');
        Schema::dropIfExists('agent_configurations');
        Schema::dropIfExists('agent_audit_logs');
        // Career Coach
        Schema::dropIfExists('career_coach_preferences');
        Schema::dropIfExists('career_coach_suggestions');
        Schema::dropIfExists('career_coach_checkins');
        Schema::dropIfExists('career_goal_updates');
        Schema::dropIfExists('career_goals');
        Schema::dropIfExists('career_coach_messages');
        Schema::dropIfExists('career_coach_sessions');
        // Negotiation
        Schema::dropIfExists('negotiation_tactics');
        Schema::dropIfExists('negotiation_messages');
        Schema::dropIfExists('negotiation_sessions');
        Schema::dropIfExists('negotiation_scripts');
        Schema::dropIfExists('negotiation_scenarios');
        Schema::dropIfExists('negotiation_strategies');
    }
};
