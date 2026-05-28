<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repair migration: creates all tables that were fake-recorded in
 * 2025_10_27_000000_sync_historical_migrations but never actually created.
 *
 * Tables from:
 * - 2025_11_06_000002 (SCOUT Corporate DNA)
 * - 2025_11_06_000003 (SCOUT Assessments)
 * - 2025_11_06_000004 (SCOUT Behavioral Assessments)
 * - 2025_11_06_000005 (Continuous Learning / Hire Performance)
 * - 2025_11_06_000007 (Bias Elimination)
 * - 2025_11_06_100000 (Skill Analyzer)
 * - 2026_05_20_152055 (Freelancer Gigs — still missing despite 000005 attempt)
 * - 2026_11_07_140000 (Talent Pipeline)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // 1. SCOUT CORPORATE DNA  (2025_11_06_000002)
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('company_dna_profiles')) {
            Schema::create('company_dna_profiles', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->text('mission_statement')->nullable();
                $table->text('vision_statement')->nullable();
                $table->json('core_values')->nullable();
                $table->json('cultural_dna')->nullable();
                $table->json('success_traits')->nullable();
                $table->json('work_style_preferences')->nullable();
                $table->json('communication_patterns')->nullable();
                $table->json('decision_making_style')->nullable();
                $table->string('company_size_category')->nullable();
                $table->string('growth_stage')->nullable();
                $table->string('industry_vertical')->nullable();
                $table->integer('employee_count')->nullable();
                $table->decimal('avg_tenure_months', 5, 1)->nullable();
                $table->decimal('retention_rate_1yr', 5, 2)->nullable();
                $table->decimal('promotion_rate', 5, 2)->nullable();
                $table->integer('dna_completeness_score')->default(0);
                $table->integer('data_quality_score')->default(0);
                $table->integer('analysis_confidence')->default(0);
                $table->timestamp('last_analyzed_at')->nullable();
                $table->integer('total_employees_analyzed')->default(0);
                $table->integer('total_hires_analyzed')->default(0);
                $table->json('ai_analysis_summary')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index('company_id');
                $table->index('dna_completeness_score');
                $table->index('last_analyzed_at');
            });
        }

        if (! Schema::hasTable('culture_analyses')) {
            Schema::create('culture_analyses', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_dna_profile_id')->constrained()->onDelete('cascade');
                $table->integer('power_distance_score')->nullable();
                $table->integer('individualism_score')->nullable();
                $table->integer('uncertainty_avoidance_score')->nullable();
                $table->integer('long_term_orientation_score')->nullable();
                $table->integer('indulgence_score')->nullable();
                $table->json('office_culture')->nullable();
                $table->json('meeting_culture')->nullable();
                $table->json('feedback_culture')->nullable();
                $table->json('recognition_patterns')->nullable();
                $table->json('collaboration_tools')->nullable();
                $table->decimal('avg_team_size', 4, 1)->nullable();
                $table->integer('cross_functional_score')->default(0);
                $table->integer('autonomy_score')->default(0);
                $table->integer('innovation_index')->default(0);
                $table->integer('learning_culture_score')->default(0);
                $table->json('professional_development')->nullable();
                $table->boolean('has_mentorship_program')->default(false);
                $table->json('diversity_metrics')->nullable();
                $table->integer('inclusion_score')->default(0);
                $table->json('dei_initiatives')->nullable();
                $table->json('culture_strengths')->nullable();
                $table->json('culture_challenges')->nullable();
                $table->json('culture_archetypes')->nullable();
                $table->text('ai_culture_summary')->nullable();
                $table->timestamps();
                $table->index('company_dna_profile_id');
                $table->index('innovation_index');
                $table->index('learning_culture_score');
            });
        }

        if (! Schema::hasTable('hiring_patterns')) {
            Schema::create('hiring_patterns', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('job_id')->nullable()->constrained()->onDelete('set null');
                $table->json('source_effectiveness')->nullable();
                $table->json('channel_conversion_rates')->nullable();
                $table->string('best_performing_channel')->nullable();
                $table->decimal('avg_time_to_hire_days', 5, 1)->nullable();
                $table->decimal('avg_time_to_fill_days', 5, 1)->nullable();
                $table->integer('avg_candidates_per_role')->nullable();
                $table->integer('avg_interviews_per_hire')->nullable();
                $table->json('successful_hire_characteristics')->nullable();
                $table->json('unsuccessful_hire_patterns')->nullable();
                $table->json('top_performer_traits')->nullable();
                $table->json('quick_departure_indicators')->nullable();
                $table->json('optimal_experience_ranges')->nullable();
                $table->json('essential_skills_by_role')->nullable();
                $table->json('nice_to_have_skills')->nullable();
                $table->json('overvalued_credentials')->nullable();
                $table->json('education_correlation')->nullable();
                $table->json('previous_company_patterns')->nullable();
                $table->json('industry_transition_success')->nullable();
                $table->json('compensation_benchmarks')->nullable();
                $table->json('offer_acceptance_rate_by_range')->nullable();
                $table->decimal('avg_negotiation_percentage', 5, 2)->nullable();
                $table->json('interview_score_vs_performance')->nullable();
                $table->json('assessment_score_vs_performance')->nullable();
                $table->json('reference_check_correlation')->nullable();
                $table->json('retention_by_hire_source')->nullable();
                $table->json('retention_by_experience_level')->nullable();
                $table->json('promotion_rate_by_hire_source')->nullable();
                $table->json('predicted_high_performer_profile')->nullable();
                $table->json('predicted_flight_risk_profile')->nullable();
                $table->json('cultural_fit_predictors')->nullable();
                $table->text('ai_hiring_recommendations')->nullable();
                $table->date('analysis_start_date')->nullable();
                $table->date('analysis_end_date')->nullable();
                $table->integer('total_hires_in_period')->default(0);
                $table->integer('confidence_score')->default(0);
                $table->timestamps();
                $table->index('company_id');
                $table->index(['analysis_start_date', 'analysis_end_date']);
                $table->index('confidence_score');
            });
        }

        if (! Schema::hasTable('success_indicators')) {
            Schema::create('success_indicators', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('employee_type')->nullable();
                $table->integer('tenure_months')->nullable();
                $table->integer('promotions_count')->default(0);
                $table->decimal('performance_rating', 3, 2)->nullable();
                $table->boolean('is_culture_champion')->default(false);
                $table->json('technical_skills')->nullable();
                $table->json('soft_skills')->nullable();
                $table->json('leadership_qualities')->nullable();
                $table->json('domain_expertise')->nullable();
                $table->json('work_preferences')->nullable();
                $table->json('communication_style')->nullable();
                $table->json('problem_solving_approach')->nullable();
                $table->json('learning_style')->nullable();
                $table->integer('values_alignment_score')->default(0);
                $table->integer('culture_fit_score')->default(0);
                $table->integer('team_collaboration_score')->default(0);
                $table->integer('initiative_score')->default(0);
                $table->string('education_level')->nullable();
                $table->json('previous_companies')->nullable();
                $table->integer('years_of_experience_at_hire')->nullable();
                $table->string('hire_source')->nullable();
                $table->json('promotion_path')->nullable();
                $table->json('skill_development_path')->nullable();
                $table->json('project_successes')->nullable();
                $table->decimal('impact_score', 5, 2)->nullable();
                $table->integer('peer_feedback_score')->default(0);
                $table->integer('mentorship_activity')->default(0);
                $table->boolean('is_knowledge_sharer')->default(false);
                $table->json('collaboration_metrics')->nullable();
                $table->json('success_factors')->nullable();
                $table->json('unique_strengths')->nullable();
                $table->json('transferable_patterns')->nullable();
                $table->text('ai_success_summary')->nullable();
                $table->timestamps();
                $table->index('company_id');
                $table->index('employee_type');
                $table->index('performance_rating');
                $table->index('culture_fit_score');
            });
        }

        if (! Schema::hasTable('team_dynamics')) {
            Schema::create('team_dynamics', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('team_name')->nullable();
                $table->string('department')->nullable();
                $table->integer('team_size')->nullable();
                $table->json('role_distribution')->nullable();
                $table->json('skill_diversity')->nullable();
                $table->decimal('avg_team_tenure_months', 5, 1)->nullable();
                $table->integer('collaboration_frequency_score')->default(0);
                $table->integer('cross_team_collaboration_score')->default(0);
                $table->decimal('meeting_hours_per_week', 4, 1)->nullable();
                $table->integer('async_communication_score')->default(0);
                $table->json('communication_channels_usage')->nullable();
                $table->json('response_time_patterns')->nullable();
                $table->json('preferred_collaboration_times')->nullable();
                $table->string('communication_style')->nullable();
                $table->integer('team_performance_score')->default(0);
                $table->integer('velocity_score')->default(0);
                $table->integer('quality_score')->default(0);
                $table->integer('innovation_score')->default(0);
                $table->integer('psychological_safety_score')->default(0);
                $table->integer('trust_level')->default(0);
                $table->integer('openness_to_feedback_score')->default(0);
                $table->boolean('has_healthy_conflict')->default(false);
                $table->string('leadership_approach')->nullable();
                $table->integer('autonomy_level')->default(0);
                $table->integer('decision_making_speed')->default(0);
                $table->json('leadership_effectiveness_metrics')->nullable();
                $table->json('team_values')->nullable();
                $table->json('working_agreements')->nullable();
                $table->json('celebration_rituals')->nullable();
                $table->json('knowledge_sharing_practices')->nullable();
                $table->decimal('avg_onboarding_time_days', 5, 1)->nullable();
                $table->integer('new_hire_integration_score')->default(0);
                $table->json('onboarding_best_practices')->nullable();
                $table->json('ideal_new_hire_traits')->nullable();
                $table->json('personality_balance_needed')->nullable();
                $table->json('skill_gaps_to_fill')->nullable();
                $table->json('cultural_additions_needed')->nullable();
                $table->json('team_strengths')->nullable();
                $table->json('team_growth_areas')->nullable();
                $table->json('compatibility_patterns')->nullable();
                $table->text('ai_team_summary')->nullable();
                $table->timestamp('last_analyzed_at')->nullable();
                $table->integer('data_points_analyzed')->default(0);
                $table->timestamps();
                $table->index('company_id');
                $table->index('department');
                $table->index('team_performance_score');
                $table->index('psychological_safety_score');
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 2. SCOUT ASSESSMENTS  (2025_11_06_000003)
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('scout_assessments')) {
            Schema::create('scout_assessments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
                $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
                $table->enum('type', ['comprehensive', 'technical', 'behavioral', 'case_study'])->default('comprehensive');
                $table->enum('status', ['pending', 'in_progress', 'completed', 'expired'])->default('pending');
                $table->integer('total_questions')->default(5);
                $table->integer('questions_answered')->default(0);
                $table->string('current_difficulty')->default('medium');
                $table->boolean('adaptive_enabled')->default(true);
                $table->integer('time_limit_minutes')->default(60);
                $table->decimal('final_score', 5, 2)->nullable();
                $table->json('performance_summary')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index('application_id');
                $table->index('job_id');
                $table->index('status');
                $table->index('type');
            });
        }

        if (! Schema::hasTable('scout_assessment_questions')) {
            Schema::create('scout_assessment_questions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('assessment_id')->constrained('scout_assessments')->onDelete('cascade');
                $table->integer('question_number');
                $table->text('question_text');
                $table->enum('question_type', ['multiple_choice', 'coding', 'essay', 'case_study']);
                $table->enum('difficulty', ['easy', 'medium', 'hard', 'expert']);
                $table->string('category');
                $table->text('expected_answer')->nullable();
                $table->json('evaluation_criteria');
                $table->integer('time_limit_seconds')->default(300);
                $table->integer('points')->default(50);
                $table->json('options')->nullable();
                $table->text('code_template')->nullable();
                $table->text('context')->nullable();
                $table->timestamps();
                $table->index('assessment_id');
                $table->index(['assessment_id', 'question_number']);
                $table->index('difficulty');
                $table->index('category');
            });
        }

        if (! Schema::hasTable('scout_assessment_responses')) {
            Schema::create('scout_assessment_responses', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('assessment_id')->constrained('scout_assessments')->onDelete('cascade');
                $table->foreignId('question_id')->constrained('scout_assessment_questions')->onDelete('cascade');
                $table->text('answer')->nullable();
                $table->longText('code_submission')->nullable();
                $table->boolean('is_correct')->default(false);
                $table->decimal('score', 5, 2)->default(0);
                $table->decimal('max_score', 5, 2);
                $table->integer('time_taken_seconds')->nullable();
                $table->integer('confidence_level')->nullable();
                $table->text('evaluation_feedback')->nullable();
                $table->json('evaluation_details')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();
                $table->index('assessment_id');
                $table->index('question_id');
                $table->unique(['assessment_id', 'question_id']);
            });
        }

        if (! Schema::hasTable('scout_assessment_analytics')) {
            Schema::create('scout_assessment_analytics', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('job_id')->nullable()->constrained('jobs')->onDelete('cascade');
                $table->string('question_category');
                $table->string('difficulty');
                $table->integer('total_attempts')->default(0);
                $table->integer('total_correct')->default(0);
                $table->decimal('average_score', 5, 2)->default(0);
                $table->decimal('average_time', 8, 2)->default(0);
                $table->json('score_distribution')->nullable();
                $table->json('time_distribution')->nullable();
                $table->date('analytics_date');
                $table->timestamps();
                $table->index('company_id');
                $table->index('job_id');
                $table->index(['question_category', 'difficulty']);
                $table->unique(
                    ['company_id', 'job_id', 'question_category', 'difficulty', 'analytics_date'],
                    'scout_assessment_analytics_unique'
                );
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3. SCOUT BEHAVIORAL ASSESSMENTS  (2025_11_06_000004)
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('scout_behavioral_assessments')) {
            Schema::create('scout_behavioral_assessments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
                $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->enum('status', ['pending', 'in_progress', 'completed', 'expired'])->default('pending');
                $table->integer('scenario_count')->default(6);
                $table->decimal('cultural_fit_score', 5, 2)->nullable();
                $table->decimal('emotional_intelligence_score', 5, 2)->nullable();
                $table->decimal('leadership_score', 5, 2)->nullable();
                $table->decimal('communication_score', 5, 2)->nullable();
                $table->decimal('approach_quality_score', 5, 2)->nullable();
                $table->decimal('reasoning_quality_score', 5, 2)->nullable();
                $table->decimal('overall_score', 5, 2)->nullable();
                $table->string('assessment_type')->default('comprehensive');
                $table->json('focus_areas')->nullable();
                $table->json('company_culture_context')->nullable();
                $table->string('thriving_likelihood')->nullable();
                $table->string('recommendation')->nullable();
                $table->json('final_insights')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['application_id', 'job_id']);
                $table->index('company_id');
                $table->index('status');
                $table->index('cultural_fit_score');
                $table->index('overall_score');
                $table->index('completed_at');
            });
        }

        if (! Schema::hasTable('scout_situational_scenarios')) {
            Schema::create('scout_situational_scenarios', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('behavioral_assessment_id')
                    ->constrained('scout_behavioral_assessments')
                    ->onDelete('cascade');
                $table->integer('scenario_number');
                $table->string('title');
                $table->text('context');
                $table->text('situation');
                $table->string('category');
                $table->enum('difficulty_level', ['easy', 'medium', 'hard', 'expert'])->default('medium');
                $table->json('valid_approaches');
                $table->integer('preferred_approach')->default(0);
                $table->json('cultural_alignment_weights')->nullable();
                $table->json('evaluates_dimensions')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['behavioral_assessment_id', 'scenario_number'], 'situational_scenarios_assessment_idx');
                $table->index('category');
                $table->index('difficulty_level');
            });
        }

        if (! Schema::hasTable('scout_scenario_responses')) {
            Schema::create('scout_scenario_responses', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('behavioral_assessment_id')
                    ->constrained('scout_behavioral_assessments')
                    ->onDelete('cascade');
                $table->foreignId('situational_scenario_id')
                    ->constrained('scout_situational_scenarios')
                    ->onDelete('cascade');
                $table->integer('selected_approach')->nullable();
                $table->text('reasoning');
                $table->integer('time_taken')->default(0);
                $table->decimal('cultural_alignment_score', 5, 2)->default(0);
                $table->decimal('approach_quality_score', 5, 2)->default(0);
                $table->decimal('reasoning_quality_score', 5, 2)->default(0);
                $table->decimal('overall_score', 5, 2)->default(0);
                $table->json('ei_dimensions_demonstrated')->nullable();
                $table->json('leadership_competencies_shown')->nullable();
                $table->json('communication_patterns_detected')->nullable();
                $table->json('strengths_identified')->nullable();
                $table->json('areas_for_improvement')->nullable();
                $table->text('ai_feedback')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index('behavioral_assessment_id');
                $table->index('situational_scenario_id');
                $table->index('cultural_alignment_score');
                $table->index('overall_score');
                $table->unique(
                    ['behavioral_assessment_id', 'situational_scenario_id'],
                    'unique_scenario_response'
                );
            });
        }

        if (! Schema::hasTable('scout_behavioral_analytics')) {
            Schema::create('scout_behavioral_analytics', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('job_id')->nullable()->constrained('jobs')->onDelete('cascade');
                $table->string('period_type')->default('all_time');
                $table->date('period_start')->nullable();
                $table->date('period_end')->nullable();
                $table->integer('total_assessments')->default(0);
                $table->integer('completed_assessments')->default(0);
                $table->decimal('avg_cultural_fit_score', 5, 2)->nullable();
                $table->decimal('avg_emotional_intelligence_score', 5, 2)->nullable();
                $table->decimal('avg_leadership_score', 5, 2)->nullable();
                $table->decimal('avg_communication_score', 5, 2)->nullable();
                $table->decimal('avg_overall_score', 5, 2)->nullable();
                $table->integer('strong_hire_count')->default(0);
                $table->integer('recommend_count')->default(0);
                $table->integer('consider_count')->default(0);
                $table->integer('caution_count')->default(0);
                $table->integer('not_recommended_count')->default(0);
                $table->json('top_ei_dimensions')->nullable();
                $table->json('top_leadership_competencies')->nullable();
                $table->json('common_communication_patterns')->nullable();
                $table->json('scenario_category_performance')->nullable();
                $table->json('high_fit_characteristics')->nullable();
                $table->json('low_fit_characteristics')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('last_calculated_at')->nullable();
                $table->timestamps();
                $table->index('company_id');
                $table->index('job_id');
                $table->index(['company_id', 'period_type']);
                $table->index(['company_id', 'job_id', 'period_type']);
                $table->index('last_calculated_at');
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 4. CONTINUOUS LEARNING / HIRE PERFORMANCE  (2025_11_06_000005)
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('hire_performance_tracking')) {
            Schema::create('hire_performance_tracking', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->date('hire_date');
                $table->string('review_period')->default('probation');
                $table->decimal('performance_rating', 3, 2);
                $table->decimal('technical_skills_rating', 3, 2)->nullable();
                $table->decimal('soft_skills_rating', 3, 2)->nullable();
                $table->decimal('cultural_fit_rating', 3, 2)->nullable();
                $table->decimal('productivity_rating', 3, 2)->nullable();
                $table->decimal('team_collaboration_rating', 3, 2)->nullable();
                $table->decimal('leadership_rating', 3, 2)->nullable();
                $table->string('retention_status')->default('active');
                $table->integer('promotion_count')->default(0);
                $table->text('manager_feedback')->nullable();
                $table->text('peer_feedback')->nullable();
                $table->json('achievements')->nullable();
                $table->json('challenges')->nullable();
                $table->json('actual_vs_predicted_performance')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['company_id', 'performance_rating']);
                $table->index(['company_id', 'retention_status']);
                $table->index(['company_id', 'review_period']);
                $table->index('hire_date');
                $table->unique(['application_id', 'review_period']);
            });
        }

        if (! Schema::hasTable('assessment_refinements')) {
            Schema::create('assessment_refinements', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->string('refinement_type');
                $table->integer('data_points_analyzed');
                $table->date('time_period_start');
                $table->date('time_period_end');
                $table->json('previous_criteria');
                $table->json('refined_criteria');
                $table->json('previous_weights');
                $table->json('refined_weights');
                $table->json('correlation_analysis');
                $table->decimal('performance_improvement_estimate', 5, 2);
                $table->decimal('confidence_score', 5, 2);
                $table->text('ai_insights')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['company_id', 'created_at']);
                $table->index('confidence_score');
            });
        }

        if (! Schema::hasTable('hiring_decision_overrides')) {
            Schema::create('hiring_decision_overrides', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('scout_recommendation');
                $table->string('manager_decision');
                $table->string('override_type');
                $table->text('override_reason')->nullable();
                $table->json('override_factors')->nullable();
                $table->string('confidence_level')->nullable();
                $table->string('outcome')->default('pending');
                $table->json('outcome_notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['company_id', 'override_type']);
                $table->index(['company_id', 'outcome']);
                $table->index('created_at');
            });
        }

        if (! Schema::hasTable('success_pattern_analytics')) {
            Schema::create('success_pattern_analytics', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('job_id')->nullable()->constrained('jobs')->onDelete('set null');
                $table->date('analysis_start_date');
                $table->date('analysis_end_date');
                $table->integer('successful_hires_count');
                $table->integer('unsuccessful_hires_count');
                $table->json('success_characteristics');
                $table->json('failure_characteristics');
                $table->json('key_differentiators');
                $table->json('correlation_strengths');
                $table->text('ai_insights')->nullable();
                $table->json('recommended_adjustments')->nullable();
                $table->decimal('pattern_confidence', 5, 2);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['company_id', 'analysis_end_date']);
                $table->index(['company_id', 'job_id']);
            });
        }

        if (! Schema::hasTable('talent_need_predictions')) {
            Schema::create('talent_need_predictions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->integer('prediction_horizon_months');
                $table->date('prediction_generated_date');
                $table->date('prediction_target_date');
                $table->json('predicted_roles');
                $table->integer('predicted_headcount');
                $table->json('predicted_skills_demand');
                $table->json('predicted_department_growth')->nullable();
                $table->json('growth_factors');
                $table->json('industry_trends');
                $table->json('seasonality_factors')->nullable();
                $table->json('prediction_basis');
                $table->decimal('confidence_score', 5, 2);
                $table->integer('data_points_used');
                $table->json('recommendations')->nullable();
                $table->text('ai_analysis')->nullable();
                $table->integer('actual_headcount')->nullable();
                $table->decimal('prediction_accuracy', 5, 2)->nullable();
                $table->json('accuracy_analysis')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['company_id', 'prediction_target_date']);
                $table->index('confidence_score');
            });
        }

        if (! Schema::hasTable('learning_insights_cache')) {
            Schema::create('learning_insights_cache', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->string('insight_type');
                $table->json('insight_data');
                $table->timestamp('generated_at');
                $table->timestamp('expires_at');
                $table->integer('data_freshness_score')->default(100);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['company_id', 'insight_type']);
                $table->index('expires_at');
                $table->unique(['company_id', 'insight_type']);
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 5. BIAS ELIMINATION  (2025_11_06_000007)
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('scout_anonymized_screenings')) {
            Schema::create('scout_anonymized_screenings', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
                $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
                $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
                $table->string('anonymized_id')->unique()->index();
                $table->json('anonymized_data');
                $table->string('original_data_hash');
                $table->enum('anonymization_level', ['minimal', 'standard', 'strict'])->default('standard');
                $table->json('removed_attributes');
                $table->boolean('is_active')->default(true)->index();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamp('deanonymized_at')->nullable();
                $table->foreignId('deanonymized_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                $table->index('company_id');
                $table->index('job_id');
                $table->index(['company_id', 'is_active']);
                $table->index(['expires_at', 'is_active']);
            });
        }

        if (! Schema::hasTable('scout_bias_audit_results')) {
            Schema::create('scout_bias_audit_results', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('audit_period_start');
                $table->timestamp('audit_period_end');
                $table->integer('total_applications_analyzed')->default(0);
                $table->decimal('bias_score', 5, 4)->index();
                $table->enum('fairness_rating', ['excellent', 'good', 'fair', 'needs_improvement', 'concerning'])->index();
                $table->json('demographic_analysis');
                $table->json('proxy_discrimination_findings');
                $table->json('decision_patterns');
                $table->json('fairness_metrics');
                $table->json('ai_detected_patterns')->nullable();
                $table->json('recommendations');
                $table->boolean('requires_attention')->default(false)->index();
                $table->timestamp('attention_acknowledged_at')->nullable();
                $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
                $table->text('action_taken')->nullable();
                $table->timestamp('remediation_completed_at')->nullable();
                $table->timestamps();
                $table->index('company_id');
                $table->index(['company_id', 'fairness_rating']);
                $table->index(['company_id', 'requires_attention']);
                $table->index('audit_period_start');
            });
        }

        if (! Schema::hasTable('scout_fairness_metrics')) {
            Schema::create('scout_fairness_metrics', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('audit_id')->nullable()->constrained('scout_bias_audit_results')->onDelete('cascade');
                $table->string('metric_type')->index();
                $table->string('metric_category')->index();
                $table->decimal('metric_value', 8, 4);
                $table->decimal('threshold_value', 8, 4)->nullable();
                $table->boolean('passes_threshold')->default(true)->index();
                $table->string('dimension')->nullable();
                $table->integer('sample_size')->default(0);
                $table->decimal('confidence_level', 5, 4)->nullable();
                $table->decimal('p_value', 6, 5)->nullable();
                $table->decimal('previous_value', 8, 4)->nullable();
                $table->enum('trend', ['improving', 'stable', 'declining', 'new'])->default('new');
                $table->json('calculation_details')->nullable();
                $table->text('interpretation')->nullable();
                $table->text('recommendation')->nullable();
                $table->timestamp('measured_at');
                $table->timestamps();
                $table->index('company_id');
                $table->index(['company_id', 'metric_type']);
                $table->index(['company_id', 'passes_threshold']);
                $table->index('measured_at');
            });
        }

        if (! Schema::hasTable('scout_proxy_discrimination_alerts')) {
            Schema::create('scout_proxy_discrimination_alerts', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('audit_id')->nullable()->constrained('scout_bias_audit_results')->onDelete('cascade');
                $table->string('indicator_type')->index();
                $table->enum('discrimination_type', [
                    'geographic', 'socioeconomic', 'age_proxy', 'ethnic_proxy', 'cultural_proxy', 'other',
                ])->index();
                $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium')->index();
                $table->decimal('correlation_strength', 5, 4);
                $table->integer('cases_analyzed')->default(0);
                $table->decimal('statistical_significance', 6, 5)->nullable();
                $table->text('impact_description');
                $table->json('affected_criteria')->nullable();
                $table->json('example_cases')->nullable();
                $table->text('recommendation');
                $table->json('suggested_actions')->nullable();
                $table->enum('status', [
                    'pending_review', 'acknowledged', 'investigating', 'resolved', 'false_positive',
                ])->default('pending_review')->index();
                $table->timestamp('reviewed_at')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->text('resolution_notes')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
                $table->index('company_id');
                $table->index(['company_id', 'severity']);
                $table->index(['company_id', 'status']);
                $table->index(['indicator_type', 'discrimination_type'], 'proxy_discrimination_indicator_idx');
            });
        }

        if (! Schema::hasTable('scout_decision_explanations')) {
            Schema::create('scout_decision_explanations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
                $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
                $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
                $table->enum('decision_type', [
                    'shortlist', 'reject', 'interview_invite', 'offer', 'final_reject',
                ])->index();
                $table->timestamp('decision_made_at');
                $table->json('primary_factors');
                $table->text('explanation');
                $table->decimal('confidence_score', 5, 4);
                $table->decimal('transparency_score', 5, 4);
                $table->json('bias_indicators')->nullable();
                $table->boolean('human_review_recommended')->default(false)->index();
                $table->text('bias_concerns')->nullable();
                $table->json('all_factors')->nullable();
                $table->json('criteria_weights')->nullable();
                $table->json('candidate_scores')->nullable();
                $table->boolean('human_reviewed')->default(false)->index();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('reviewed_at')->nullable();
                $table->text('reviewer_notes')->nullable();
                $table->boolean('decision_overridden')->default(false);
                $table->text('override_reason')->nullable();
                $table->timestamps();
                $table->index('company_id');
                $table->index(['company_id', 'decision_type']);
                $table->index(['company_id', 'human_review_recommended'], 'decision_human_review_idx');
                $table->index('decision_made_at');
            });
        }

        if (! Schema::hasTable('scout_diversity_analytics')) {
            Schema::create('scout_diversity_analytics', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
                $table->date('period_start');
                $table->date('period_end');
                $table->enum('period_type', ['weekly', 'monthly', 'quarterly', 'annual'])->index();
                $table->integer('total_applications')->default(0);
                $table->integer('total_hires')->default(0);
                $table->integer('minimum_group_size')->default(10);
                $table->json('application_stage_distribution')->nullable();
                $table->json('role_distribution')->nullable();
                $table->json('seniority_distribution')->nullable();
                $table->decimal('overall_retention_rate', 5, 2)->nullable();
                $table->json('retention_by_cohort')->nullable();
                $table->decimal('avg_tenure_months', 6, 2)->nullable();
                $table->decimal('pay_equity_score', 5, 4)->nullable();
                $table->json('compensation_distribution')->nullable();
                $table->json('pay_gap_analysis')->nullable();
                $table->json('inclusion_metrics')->nullable();
                $table->decimal('inclusion_index', 5, 2)->nullable();
                $table->boolean('meets_privacy_threshold')->default(true);
                $table->text('privacy_notes')->nullable();
                $table->boolean('data_anonymized')->default(true);
                $table->enum('diversity_trend', ['improving', 'stable', 'declining'])->nullable();
                $table->enum('inclusion_trend', ['improving', 'stable', 'declining'])->nullable();
                $table->timestamps();
                $table->index('company_id');
                $table->index(['company_id', 'period_type']);
                $table->index('period_start');
                $table->index(['company_id', 'period_start', 'period_end'], 'diversity_period_span_idx');
            });
        }

        if (! Schema::hasTable('scout_bias_mitigation_actions')) {
            Schema::create('scout_bias_mitigation_actions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('audit_id')->nullable()->constrained('scout_bias_audit_results')->onDelete('cascade');
                $table->foreignId('alert_id')->nullable()->constrained('scout_proxy_discrimination_alerts')->onDelete('cascade');
                $table->enum('action_type', [
                    'criteria_adjustment', 'process_change', 'training', 'policy_update',
                    'technology_update', 'review_process', 'other',
                ])->index();
                $table->string('action_title');
                $table->text('action_description');
                $table->timestamp('planned_start_date')->nullable();
                $table->timestamp('planned_completion_date')->nullable();
                $table->foreignId('responsible_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('status', [
                    'planned', 'in_progress', 'completed', 'delayed', 'cancelled',
                ])->default('planned')->index();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->integer('progress_percentage')->default(0);
                $table->json('expected_impact')->nullable();
                $table->json('actual_impact')->nullable();
                $table->text('impact_notes')->nullable();
                $table->boolean('requires_verification')->default(true);
                $table->timestamp('verified_at')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
                $table->decimal('effectiveness_score', 5, 4)->nullable();
                $table->timestamps();
                $table->index('company_id');
                $table->index(['company_id', 'status']);
                $table->index(['company_id', 'action_type']);
            });
        }

        if (! Schema::hasTable('scout_training_data_validation')) {
            Schema::create('scout_training_data_validation', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
                $table->string('dataset_type')->index();
                $table->integer('total_records')->default(0);
                $table->timestamp('data_period_start');
                $table->timestamp('data_period_end');
                $table->json('representation_metrics');
                $table->decimal('diversity_score', 5, 4);
                $table->boolean('meets_diversity_threshold')->default(false)->index();
                $table->decimal('minimum_threshold', 5, 4)->default(0.70);
                $table->json('imbalanced_dimensions')->nullable();
                $table->json('underrepresented_groups')->nullable();
                $table->integer('smallest_group_size')->nullable();
                $table->integer('missing_data_count')->default(0);
                $table->decimal('data_quality_score', 5, 4)->nullable();
                $table->json('data_quality_issues')->nullable();
                $table->json('recommendations')->nullable();
                $table->boolean('approved_for_training')->default(false)->index();
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('validated_at');
                $table->foreignId('validated_by')->constrained('users')->onDelete('cascade');
                $table->timestamps();
                $table->index('company_id');
                $table->index(['company_id', 'dataset_type']);
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 6. SKILL ANALYZER  (2025_11_06_100000)
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('user_skills')) {
            Schema::create('user_skills', function (Blueprint $table): void {
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

        if (! Schema::hasTable('skill_gaps')) {
            Schema::create('skill_gaps', function (Blueprint $table): void {
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

        if (! Schema::hasTable('learning_paths')) {
            Schema::create('learning_paths', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('skill_gap_id')->nullable()->constrained()->onDelete('set null');
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

        if (! Schema::hasTable('learning_resources')) {
            Schema::create('learning_resources', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('learning_path_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('url')->nullable();
                $table->enum('resource_type', [
                    'course', 'video', 'article', 'book', 'tutorial', 'project',
                    'documentation', 'podcast', 'interactive',
                ])->default('article');
                $table->enum('provider', [
                    'coursera', 'udemy', 'pluralsight', 'youtube', 'medium', 'github',
                    'official_docs', 'free_code_camp', 'khan_academy', 'other',
                ])->default('other');
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

        if (! Schema::hasTable('skill_assessments')) {
            Schema::create('skill_assessments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_skill_id')->nullable()->constrained('user_skills')->onDelete('set null');
                $table->string('skill_name');
                $table->string('assessment_title');
                $table->text('description')->nullable();
                $table->enum('assessment_type', ['multiple_choice', 'coding', 'scenario_based', 'project', 'mixed'])->default('multiple_choice');
                $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
                $table->json('questions')->nullable();
                $table->integer('total_questions')->default(0);
                $table->integer('passing_score')->default(70);
                $table->integer('time_limit_minutes')->nullable();
                $table->json('answers')->nullable();
                $table->integer('score')->nullable();
                $table->boolean('passed')->nullable();
                $table->enum('proficiency_awarded', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable();
                $table->json('detailed_results')->nullable();
                $table->json('strengths')->nullable();
                $table->json('weaknesses')->nullable();
                $table->json('recommendations')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_shareable')->default(false);
                $table->string('certificate_url')->nullable();
                $table->string('certificate_hash')->unique()->nullable();
                $table->enum('status', ['draft', 'in_progress', 'submitted', 'graded', 'expired'])->default('draft');
                $table->timestamps();
                $table->softDeletes();
                $table->index(['user_id', 'skill_name']);
                $table->index(['user_id', 'status']);
                $table->index('passed');
                $table->index('certificate_hash');
            });
        }

        if (! Schema::hasTable('learning_progress')) {
            Schema::create('learning_progress', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('learning_path_id')->nullable()->constrained('learning_paths')->onDelete('cascade');
                $table->foreignId('learning_resource_id')->nullable()->constrained('learning_resources')->onDelete('cascade');
                $table->date('progress_date');
                $table->integer('time_spent_minutes')->default(0);
                $table->decimal('completion_percentage', 5, 2)->default(0);
                $table->enum('activity_type', ['watching', 'reading', 'coding', 'quiz', 'project', 'practice'])->default('reading');
                $table->text('notes')->nullable();
                $table->json('achievements')->nullable();
                $table->integer('streak_days')->default(0);
                $table->boolean('daily_goal_met')->default(false);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'progress_date']);
                $table->index('learning_path_id');
                $table->index('learning_resource_id');
                $table->index('daily_goal_met');
            });
        }

        if (! Schema::hasTable('skill_validations')) {
            Schema::create('skill_validations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_skill_id')->nullable()->constrained('user_skills')->onDelete('cascade');
                $table->string('skill_name');
                $table->enum('validation_source', [
                    'work_history', 'project', 'education', 'certification', 'endorsement', 'assessment',
                ])->default('work_history');
                $table->text('evidence_description')->nullable();
                $table->json('evidence_data')->nullable();
                $table->integer('confidence_score')->default(0);
                $table->enum('proficiency_detected', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable();
                $table->integer('years_of_experience')->nullable();
                $table->json('key_achievements')->nullable();
                $table->json('projects')->nullable();
                $table->json('ai_analysis')->nullable();
                $table->json('demonstration_suggestions')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['user_id', 'skill_name']);
                $table->index('user_skill_id');
                $table->index('validation_source');
                $table->index('confidence_score');
                $table->index('is_verified');
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 7. FREELANCER GIGS  (2026_05_20_152055)
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('freelancer_gigs')) {
            Schema::create('freelancer_gigs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('freelancer_profile_id');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description');
                $table->string('category');
                $table->json('packages');
                $table->json('tags')->nullable();
                $table->json('faq')->nullable();
                $table->text('requirements')->nullable();
                $table->string('status')->default('active');
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('views_count')->default(0);
                $table->unsignedInteger('orders_count')->default(0);
                $table->decimal('average_rating', 3, 2)->default(5.00);
                $table->unsignedInteger('total_reviews')->default(0);
                $table->timestamps();
                $table->softDeletes();
                $table->index(['status', 'is_featured']);
                $table->index('category');
                $table->index('freelancer_profile_id');
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 8. TALENT PIPELINE  (2026_11_07_140000)
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('talent_pipelines')) {
            Schema::create('talent_pipelines', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('pipeline_name');
                $table->string('target_role');
                $table->text('role_description')->nullable();
                $table->enum('pipeline_status', ['active', 'paused', 'archived'])->default('active');
                $table->enum('pipeline_type', [
                    'recurring_role', 'critical_position', 'growth_initiative', 'succession_planning',
                ])->default('recurring_role');
                $table->json('required_skills')->nullable();
                $table->json('preferred_experience')->nullable();
                $table->json('cultural_fit_criteria')->nullable();
                $table->integer('target_pipeline_size')->default(10);
                $table->integer('current_pipeline_size')->default(0);
                $table->decimal('pipeline_health_score', 5, 2)->default(0);
                $table->integer('hiring_frequency_days')->nullable();
                $table->date('last_hired_at')->nullable();
                $table->date('next_projected_hire_date')->nullable();
                $table->json('pipeline_metrics')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['company_id', 'pipeline_status']);
                $table->index(['target_role', 'pipeline_status']);
                $table->index('pipeline_health_score');
            });
        }

        if (! Schema::hasTable('pipeline_candidates')) {
            Schema::create('pipeline_candidates', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('talent_pipeline_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('pipeline_stage', [
                    'sourced', 'engaged', 'qualified', 'pre_screened',
                    'warm', 'hot', 'cool', 'archived',
                ])->default('sourced');
                $table->decimal('match_score', 5, 2)->default(0);
                $table->decimal('dna_compatibility_score', 5, 2)->default(0);
                $table->date('added_to_pipeline_at');
                $table->date('last_engaged_at')->nullable();
                $table->integer('engagement_count')->default(0);
                $table->enum('engagement_preference', ['email', 'phone', 'linkedin', 'no_contact'])->default('email');
                $table->json('interaction_history')->nullable();
                $table->json('skill_assessment')->nullable();
                $table->text('sourcing_notes')->nullable();
                $table->text('engagement_notes')->nullable();
                $table->enum('availability_status', [
                    'immediately_available', 'available_soon', 'passive', 'not_available',
                ])->default('passive');
                $table->decimal('expected_salary_min', 12, 2)->nullable();
                $table->decimal('expected_salary_max', 12, 2)->nullable();
                $table->date('next_follow_up_date')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['talent_pipeline_id', 'user_id']);
                $table->index(['pipeline_stage', 'match_score']);
                $table->index('next_follow_up_date');
                $table->index('last_engaged_at');
            });
        }

        if (! Schema::hasTable('silver_medalists')) {
            Schema::create('silver_medalists', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('job_id')->constrained()->onDelete('cascade');
                $table->foreignId('application_id')->nullable()->constrained()->onDelete('set null');
                $table->enum('silver_medal_reason', [
                    'strong_second_choice', 'overqualified', 'timing_mismatch',
                    'budget_constraints', 'team_fit_preference', 'skill_mismatch_minor', 'cultural_potential',
                ]);
                $table->decimal('interview_score', 5, 2)->nullable();
                $table->decimal('skill_score', 5, 2)->nullable();
                $table->decimal('cultural_fit_score', 5, 2)->nullable();
                $table->json('strengths')->nullable();
                $table->json('development_areas')->nullable();
                $table->text('interviewer_feedback')->nullable();
                $table->text('ai_recommendation')->nullable();
                $table->json('suitable_future_roles')->nullable();
                $table->enum('re_engagement_status', [
                    'not_contacted', 'contacted', 'interested', 'not_interested', 'hired_elsewhere',
                ])->default('not_contacted');
                $table->date('silver_medal_date');
                $table->date('last_contacted_at')->nullable();
                $table->integer('contact_attempts')->default(0);
                $table->date('next_reach_out_date')->nullable();
                $table->boolean('added_to_talent_pipeline')->default(false);
                $table->foreignId('talent_pipeline_id')->nullable()->constrained('talent_pipelines')->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();
                $table->index(['company_id', 're_engagement_status']);
                $table->index('next_reach_out_date');
                $table->index(['silver_medal_date', 're_engagement_status']);
            });
        }

        if (! Schema::hasTable('passive_candidate_profiles')) {
            Schema::create('passive_candidate_profiles', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('source_platform')->nullable();
                $table->string('external_profile_url')->nullable();
                $table->string('candidate_name');
                $table->string('candidate_email')->nullable();
                $table->string('candidate_phone')->nullable();
                $table->string('current_company')->nullable();
                $table->string('current_title')->nullable();
                $table->string('location')->nullable();
                $table->json('skills')->nullable();
                $table->json('experience_summary')->nullable();
                $table->integer('years_of_experience')->nullable();
                $table->decimal('dna_match_score', 5, 2)->default(0);
                $table->json('dna_alignment_factors')->nullable();
                $table->json('potential_roles')->nullable();
                $table->enum('discovery_method', [
                    'ai_market_scan', 'referral', 'event_attendance',
                    'social_media', 'competitor_analysis', 'industry_research',
                ])->default('ai_market_scan');
                $table->enum('engagement_readiness', ['not_ready', 'monitor', 'ready', 'urgent'])->default('monitor');
                $table->json('engagement_signals')->nullable();
                $table->date('discovered_at');
                $table->date('last_monitored_at')->nullable();
                $table->date('optimal_engagement_date')->nullable();
                $table->boolean('engagement_initiated')->default(false);
                $table->date('engaged_at')->nullable();
                $table->enum('engagement_outcome', [
                    'no_response', 'interested', 'not_interested',
                    'converted_to_applicant', 'added_to_pipeline',
                ])->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['company_id', 'engagement_readiness']);
                $table->index('dna_match_score');
                $table->index('optimal_engagement_date');
                $table->index(['engagement_initiated', 'engagement_readiness'], 'passive_candidate_engagement_index');
            });
        }

        if (! Schema::hasTable('candidate_interactions')) {
            Schema::create('candidate_interactions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('application_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('job_id')->nullable()->constrained()->onDelete('cascade');
                $table->enum('interaction_type', [
                    'application_submitted', 'application_viewed', 'status_update_sent',
                    'email_sent', 'phone_call', 'interview_scheduled', 'interview_completed',
                    'assessment_sent', 'assessment_completed', 'offer_extended', 'offer_accepted',
                    'offer_declined', 'rejection_sent', 'feedback_provided', 'follow_up_contact',
                    'pipeline_addition', 'pipeline_engagement', 'silver_medal_notification',
                    're_engagement_attempt',
                ]);
                $table->string('interaction_channel')->nullable();
                $table->text('interaction_summary')->nullable();
                $table->json('interaction_metadata')->nullable();
                $table->boolean('automated')->default(false);
                $table->foreignId('initiated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('candidate_sentiment', ['positive', 'neutral', 'negative', 'unknown'])->default('unknown');
                $table->text('candidate_feedback')->nullable();
                $table->integer('response_time_hours')->nullable();
                $table->timestamp('interacted_at');
                $table->timestamps();
                $table->index(['user_id', 'interacted_at']);
                $table->index(['company_id', 'interaction_type']);
                $table->index('interacted_at');
            });
        }

        if (! Schema::hasTable('employer_brand_scores')) {
            Schema::create('employer_brand_scores', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->date('measurement_date');
                $table->enum('measurement_period', ['daily', 'weekly', 'monthly', 'quarterly'])->default('weekly');
                $table->decimal('overall_brand_score', 5, 2)->default(0);
                $table->decimal('application_experience_score', 5, 2)->default(0);
                $table->decimal('communication_score', 5, 2)->default(0);
                $table->decimal('interview_experience_score', 5, 2)->default(0);
                $table->decimal('feedback_quality_score', 5, 2)->default(0);
                $table->decimal('transparency_score', 5, 2)->default(0);
                $table->decimal('respect_score', 5, 2)->default(0);
                $table->decimal('average_response_time_hours', 8, 2)->nullable();
                $table->integer('total_interactions')->default(0);
                $table->integer('positive_interactions')->default(0);
                $table->integer('negative_interactions')->default(0);
                $table->integer('feedback_requests_sent')->default(0);
                $table->integer('feedback_responses_received')->default(0);
                $table->decimal('feedback_response_rate', 5, 2)->default(0);
                $table->json('positive_feedback_themes')->nullable();
                $table->json('negative_feedback_themes')->nullable();
                $table->json('improvement_recommendations')->nullable();
                $table->decimal('industry_benchmark_score', 5, 2)->nullable();
                $table->enum('brand_health_trend', ['improving', 'stable', 'declining'])->default('stable');
                $table->timestamps();
                $table->unique(
                    ['company_id', 'measurement_date', 'measurement_period'],
                    'employer_brand_scores_measurement_unique'
                );
                $table->index(['company_id', 'measurement_date']);
                $table->index('overall_brand_score');
            });
        }

        if (! Schema::hasTable('candidate_feedback')) {
            Schema::create('candidate_feedback', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('application_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('job_id')->nullable()->constrained()->onDelete('cascade');
                $table->enum('feedback_type', [
                    'application_experience', 'interview_experience', 'rejection_feedback',
                    'offer_feedback', 'general_experience',
                ]);
                $table->enum('feedback_trigger', ['requested', 'voluntary', 'follow_up'])->default('requested');
                $table->integer('overall_rating')->nullable();
                $table->integer('application_process_rating')->nullable();
                $table->integer('communication_rating')->nullable();
                $table->integer('interview_rating')->nullable();
                $table->integer('respect_rating')->nullable();
                $table->integer('transparency_rating')->nullable();
                $table->text('positive_experience')->nullable();
                $table->text('negative_experience')->nullable();
                $table->text('improvement_suggestions')->nullable();
                $table->text('general_comments')->nullable();
                $table->boolean('would_recommend')->nullable();
                $table->boolean('would_apply_again')->nullable();
                $table->integer('likelihood_to_recommend')->nullable();
                $table->timestamp('feedback_requested_at')->nullable();
                $table->timestamp('feedback_submitted_at')->nullable();
                $table->integer('response_time_days')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['company_id', 'feedback_submitted_at']);
                $table->index('overall_rating');
                $table->index('likelihood_to_recommend');
            });
        }
    }

    public function down(): void
    {
        // Drop in reverse dependency order
        Schema::dropIfExists('candidate_feedback');
        Schema::dropIfExists('employer_brand_scores');
        Schema::dropIfExists('candidate_interactions');
        Schema::dropIfExists('passive_candidate_profiles');
        Schema::dropIfExists('silver_medalists');
        Schema::dropIfExists('pipeline_candidates');
        Schema::dropIfExists('talent_pipelines');
        Schema::dropIfExists('freelancer_gigs');
        Schema::dropIfExists('skill_validations');
        Schema::dropIfExists('learning_progress');
        Schema::dropIfExists('skill_assessments');
        Schema::dropIfExists('learning_resources');
        Schema::dropIfExists('learning_paths');
        Schema::dropIfExists('skill_gaps');
        Schema::dropIfExists('user_skills');
        Schema::dropIfExists('scout_training_data_validation');
        Schema::dropIfExists('scout_bias_mitigation_actions');
        Schema::dropIfExists('scout_diversity_analytics');
        Schema::dropIfExists('scout_decision_explanations');
        Schema::dropIfExists('scout_proxy_discrimination_alerts');
        Schema::dropIfExists('scout_fairness_metrics');
        Schema::dropIfExists('scout_bias_audit_results');
        Schema::dropIfExists('scout_anonymized_screenings');
        Schema::dropIfExists('learning_insights_cache');
        Schema::dropIfExists('talent_need_predictions');
        Schema::dropIfExists('success_pattern_analytics');
        Schema::dropIfExists('hiring_decision_overrides');
        Schema::dropIfExists('assessment_refinements');
        Schema::dropIfExists('hire_performance_tracking');
        Schema::dropIfExists('scout_behavioral_analytics');
        Schema::dropIfExists('scout_scenario_responses');
        Schema::dropIfExists('scout_situational_scenarios');
        Schema::dropIfExists('scout_behavioral_assessments');
        Schema::dropIfExists('scout_assessment_analytics');
        Schema::dropIfExists('scout_assessment_responses');
        Schema::dropIfExists('scout_assessment_questions');
        Schema::dropIfExists('scout_assessments');
        Schema::dropIfExists('team_dynamics');
        Schema::dropIfExists('success_indicators');
        Schema::dropIfExists('hiring_patterns');
        Schema::dropIfExists('culture_analyses');
        Schema::dropIfExists('company_dna_profiles');
    }
};
