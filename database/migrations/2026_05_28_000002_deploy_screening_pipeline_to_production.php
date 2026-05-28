<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCTION SCREENING PIPELINE DEPLOYMENT — 2026-05-28
 *
 * Creates ALL tables required for the complete screening, testing, interview,
 * background-check, video-interview, company-review, and agent-matching features.
 *
 * Every block is guarded with Schema::hasTable / Schema::hasColumn so this
 * migration is fully idempotent — safe to run on any environment.
 *
 * Tables created:
 *   question_banks, evaluation_sessions, evaluation_answers, bulk_email_logs
 *   hiring_tests, hiring_test_attempts, hiring_rounds
 *   interviews, interview_panelists, interview_panel_scores
 *   background_check_packages, background_checks, background_check_items, background_check_activities
 *   video_interview_sessions, video_interview_questions, video_interview_recordings
 *   video_interview_analyses, video_interview_invitations
 *   company_reviews, review_helpful, company_user
 *   agent_internal_matches
 *
 * Columns added to existing tables:
 *   applications  — hiring_stage, pipeline_stage_date, pipeline_stage_notes,
 *                   confirmation_email_sent, test_link_token, access_token
 *   job_listings  — open_date, close_date, eval_start_date, final_date,
 *                   target_hire_count, application_link_token, orin_generated_jd,
 *                   orin_application_form_fields, application_phase,
 *                   requires_portfolio, requires_github, requires_work_sample,
 *                   mandatory_screening_questions
 *   agent_configurations — is_paused, paused_at, pause_reason
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // 1. ORIN™ EVALUATION TABLES
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('question_banks')) {
            Schema::create('question_banks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
                $table->enum('difficulty', ['foundational', 'intermediate', 'advanced']);
                $table->enum('question_type', ['mcq', 'short_answer', 'scenario', 'code_snippet', 'case_study', 'video_response']);
                $table->string('topic')->nullable();
                $table->text('question_text');
                $table->json('options')->nullable();
                $table->text('correct_answer')->nullable();
                $table->text('evaluation_rubric')->nullable();
                $table->unsignedInteger('time_limit_seconds')->default(120);
                $table->unsignedInteger('max_score')->default(10);
                $table->boolean('is_behavioural')->default(false);
                $table->boolean('is_culture_fit')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['job_id', 'difficulty', 'question_type']);
            });
        }

        if (! Schema::hasTable('evaluation_sessions')) {
            Schema::create('evaluation_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained()->cascadeOnDelete();
                $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                $table->enum('status', ['not_started', 'in_progress', 'paused', 'completed', 'expired'])->default('not_started');
                $table->string('session_token', 64)->unique();
                $table->string('redis_key', 128)->nullable();
                $table->json('assigned_question_ids');
                $table->unsignedInteger('current_question_index')->default(0);
                $table->unsignedInteger('total_questions');
                $table->enum('current_difficulty', ['foundational', 'intermediate', 'advanced'])->default('foundational');
                $table->unsignedInteger('consecutive_correct')->default(0);
                $table->unsignedInteger('consecutive_incorrect')->default(0);
                $table->unsignedInteger('tab_switch_count')->default(0);
                $table->unsignedInteger('focus_loss_count')->default(0);
                $table->json('time_anomalies')->nullable();
                $table->boolean('flagged_for_review')->default(false);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->unsignedInteger('total_time_seconds')->nullable();
                $table->decimal('raw_score', 5, 2)->nullable();
                $table->decimal('weighted_score', 5, 2)->nullable();
                $table->json('section_scores')->nullable();
                $table->timestamps();
                $table->index(['application_id', 'status']);
                $table->index(['job_id', 'status']);
            });
        }

        if (! Schema::hasTable('evaluation_answers')) {
            Schema::create('evaluation_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('evaluation_session_id')->constrained()->cascadeOnDelete();
                $table->foreignId('question_id')->constrained('question_banks')->cascadeOnDelete();
                $table->unsignedInteger('question_index');
                $table->text('answer_text')->nullable();
                $table->json('answer_options')->nullable();
                $table->string('video_response_url')->nullable();
                $table->decimal('score_awarded', 5, 2)->nullable();
                $table->decimal('max_score', 5, 2)->default(10);
                $table->text('ai_feedback')->nullable();
                $table->boolean('is_correct')->nullable();
                $table->boolean('is_auto_scored')->default(false);
                $table->unsignedInteger('time_taken_seconds')->nullable();
                $table->boolean('time_anomaly')->default(false);
                $table->timestamp('answered_at')->nullable();
                $table->timestamps();
                $table->index(['evaluation_session_id', 'question_index']);
            });
        }

        if (! Schema::hasTable('bulk_email_logs')) {
            Schema::create('bulk_email_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
                $table->enum('email_type', [
                    'application_received', 'evaluation_open', 'shortlisted',
                    'rejected', 'evaluation_reminder', 'results_ready',
                ]);
                $table->unsignedInteger('total_recipients');
                $table->unsignedInteger('sent_count')->default(0);
                $table->unsignedInteger('failed_count')->default(0);
                $table->enum('status', ['pending', 'processing', 'complete', 'failed'])->default('pending');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->json('failed_recipients')->nullable();
                $table->timestamps();
                $table->index(['job_id', 'email_type', 'status']);
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 2. HIRING TESTS (MCQ TESTS PER JOB STAGE)
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('hiring_tests')) {
            Schema::create('hiring_tests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
                $table->string('stage', 50); // company_info_test | aptitude | one_on_one
                $table->string('title');
                $table->text('instructions')->nullable();
                $table->json('questions'); // [{question, options:[], correct_index, marks}]
                $table->unsignedInteger('pass_score')->default(60); // percentage
                $table->unsignedInteger('time_limit_minutes')->default(30);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['job_id', 'stage']);
            });
        }

        if (! Schema::hasTable('hiring_test_attempts')) {
            Schema::create('hiring_test_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained()->cascadeOnDelete();
                $table->foreignId('hiring_test_id')->constrained()->cascadeOnDelete();
                $table->string('stage', 50);
                $table->json('answers')->nullable();
                $table->unsignedInteger('score')->nullable();
                $table->boolean('passed')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();
                $table->unique(['application_id', 'stage']);
            });
        }

        if (! Schema::hasTable('hiring_rounds')) {
            Schema::create('hiring_rounds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
                $table->string('name');
                $table->enum('type', [
                    'info_test', 'aptitude', 'technical', 'hr_interview',
                    'culture_fit', 'practical', 'portfolio_review',
                ]);
                $table->unsignedTinyInteger('round_order')->default(1);
                $table->text('description')->nullable();
                $table->text('ai_evaluation_criteria')->nullable();
                $table->date('test_date')->nullable();
                $table->unsignedTinyInteger('evaluation_days')->default(5);
                $table->date('evaluation_date')->nullable();
                $table->enum('status', ['pending', 'active', 'evaluating', 'completed'])->default('pending');
                $table->timestamps();
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3. INTERVIEW MANAGEMENT TABLES
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('interviews')) {
            Schema::create('interviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained()->cascadeOnDelete();
                $table->string('interview_type')->default('video'); // phone, video, onsite, technical, behavioral, panel
                $table->dateTime('scheduled_at')->nullable();
                $table->integer('duration_minutes')->default(60);
                $table->string('location')->nullable();
                $table->string('meeting_link')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, canceled, no_show
                $table->integer('round')->default(1);
                $table->json('question_set')->nullable();
                $table->json('feedback')->nullable();
                $table->decimal('rating', 3, 1)->nullable();
                $table->text('interviewer_notes')->nullable();
                $table->string('ai_recommendation')->nullable();
                $table->json('ai_score_summary')->nullable();
                $table->dateTime('started_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->dateTime('canceled_at')->nullable();
                $table->string('cancellation_reason')->nullable();
                $table->timestamps();
                $table->index(['application_id', 'status']);
                $table->index('scheduled_at');
            });
        }

        if (! Schema::hasTable('interview_panelists')) {
            Schema::create('interview_panelists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('interview_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->boolean('is_lead')->default(false);
                $table->string('status')->default('invited');
                $table->timestamps();
                $table->unique(['interview_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('interview_panel_scores')) {
            Schema::create('interview_panel_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('interview_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('question_key');
                $table->integer('score')->default(3);
                $table->text('comment')->nullable();
                $table->timestamps();
                $table->index(['interview_id', 'user_id']);
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 4. BACKGROUND CHECKS
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('background_check_packages')) {
            Schema::create('background_check_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('provider');
                $table->string('provider_package_id')->nullable();
                $table->json('checks_included');
                $table->decimal('price', 10, 2)->nullable();
                $table->integer('estimated_days')->default(3);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
                $table->index(['company_id', 'is_active']);
            });
        }

        if (! Schema::hasTable('background_checks')) {
            Schema::create('background_checks', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('application_id')->nullable()->constrained('applications')->nullOnDelete();
                $table->foreignId('package_id')->nullable()->constrained('background_check_packages')->nullOnDelete();
                $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
                $table->string('provider');
                $table->string('provider_check_id')->nullable();
                $table->string('provider_report_id')->nullable();
                $table->string('provider_candidate_id')->nullable();
                $table->string('status')->default('pending');
                $table->string('result')->nullable();
                $table->string('adjudication')->nullable();
                $table->timestamp('consent_requested_at')->nullable();
                $table->timestamp('consent_received_at')->nullable();
                $table->timestamp('consent_expires_at')->nullable();
                $table->string('consent_token')->nullable()->unique();
                $table->string('consent_ip_address')->nullable();
                $table->text('consent_user_agent')->nullable();
                $table->boolean('consent_given')->default(false);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->integer('estimated_completion_days')->nullable();
                $table->json('checks_requested')->nullable();
                $table->json('checks_completed')->nullable();
                $table->json('report_summary')->nullable();
                $table->text('report_url')->nullable();
                $table->string('report_pdf_path')->nullable();
                $table->decimal('cost', 10, 2)->nullable();
                $table->text('notes')->nullable();
                $table->text('internal_notes')->nullable();
                $table->boolean('has_flags')->default(false);
                $table->json('flags')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['company_id', 'status']);
                $table->index(['candidate_id', 'status']);
                $table->index('consent_token');
            });
        }

        if (! Schema::hasTable('background_check_items')) {
            Schema::create('background_check_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('background_check_id')->constrained()->cascadeOnDelete();
                $table->string('check_type');
                $table->string('status')->default('pending');
                $table->string('result')->nullable();
                $table->json('result_data')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->index(['background_check_id', 'check_type']);
            });
        }

        if (! Schema::hasTable('background_check_activities')) {
            Schema::create('background_check_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('background_check_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action');
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
                $table->index(['background_check_id', 'created_at']);
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 5. VIDEO INTERVIEW TABLES
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('video_interview_sessions')) {
            Schema::create('video_interview_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('job_id')->nullable()->constrained('job_listings')->nullOnDelete();
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedBigInteger('interview_session_id')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('type', ['async', 'live', 'mock'])->default('async');
                $table->enum('status', ['pending', 'in_progress', 'completed', 'expired', 'cancelled'])->default('pending');
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->integer('max_duration_minutes')->default(60);
                $table->integer('actual_duration_seconds')->nullable();
                $table->string('room_id')->nullable()->unique();
                $table->string('room_token')->nullable();
                $table->json('participants')->nullable();
                $table->boolean('has_screen_share')->default(false);
                $table->boolean('is_recording_enabled')->default(true);
                $table->json('ai_analysis_summary')->nullable();
                $table->decimal('overall_score', 5, 2)->nullable();
                $table->json('performance_breakdown')->nullable();
                $table->json('settings')->nullable();
                $table->boolean('allow_retakes')->default(false);
                $table->integer('max_retakes')->default(1);
                $table->timestamps();
                $table->softDeletes();
                $table->index(['user_id', 'status']);
                $table->index(['type', 'status']);
                $table->index('scheduled_at');
            });
        }

        if (! Schema::hasTable('video_interview_questions')) {
            Schema::create('video_interview_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('video_interview_session_id')->constrained()->cascadeOnDelete();
                $table->integer('order')->default(1);
                $table->text('question_text');
                $table->text('question_context')->nullable();
                $table->enum('question_type', ['behavioral', 'technical', 'situational', 'general'])->default('general');
                $table->integer('prep_time_seconds')->default(30);
                $table->integer('max_response_time_seconds')->default(180);
                $table->integer('min_response_time_seconds')->default(30);
                $table->integer('max_retakes')->default(2);
                $table->boolean('allow_skip')->default(false);
                $table->json('expected_elements')->nullable();
                $table->json('keywords_to_look_for')->nullable();
                $table->text('ideal_answer_notes')->nullable();
                $table->timestamps();
                $table->index(['video_interview_session_id', 'order'], 'vi_questions_session_order_idx');
            });
        }

        if (! Schema::hasTable('video_interview_recordings')) {
            Schema::create('video_interview_recordings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('video_interview_session_id')->constrained()->cascadeOnDelete();
                $table->foreignId('video_interview_question_id')->nullable()->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->enum('recording_type', ['response', 'full_session', 'screen_share'])->default('response');
                $table->integer('attempt_number')->default(1);
                $table->enum('status', ['uploading', 'processing', 'ready', 'failed', 'deleted'])->default('uploading');
                $table->string('storage_disk')->default('s3');
                $table->string('file_path');
                $table->string('file_name');
                $table->string('mime_type')->default('video/webm');
                $table->unsignedBigInteger('file_size')->nullable();
                $table->integer('duration_seconds')->nullable();
                $table->string('thumbnail_path')->nullable();
                $table->json('thumbnail_sprites')->nullable();
                $table->string('playback_url')->nullable();
                $table->string('download_url')->nullable();
                $table->timestamp('url_expires_at')->nullable();
                $table->longText('transcription')->nullable();
                $table->json('transcription_segments')->nullable();
                $table->string('transcription_language')->default('en');
                $table->enum('transcription_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
                $table->json('processing_metadata')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['video_interview_session_id', 'recording_type'], 'vi_rec_session_type_idx');
                $table->index(['user_id', 'status'], 'vi_rec_user_status_idx');
            });
        }

        if (! Schema::hasTable('video_interview_analyses')) {
            Schema::create('video_interview_analyses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('video_interview_recording_id')->constrained()->cascadeOnDelete();
                $table->foreignId('video_interview_question_id')->nullable()->constrained()->cascadeOnDelete();
                $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
                $table->decimal('content_score', 5, 2)->nullable();
                $table->decimal('clarity_score', 5, 2)->nullable();
                $table->decimal('structure_score', 5, 2)->nullable();
                $table->decimal('relevance_score', 5, 2)->nullable();
                $table->json('key_points_mentioned')->nullable();
                $table->json('missing_elements')->nullable();
                $table->json('star_analysis')->nullable();
                $table->decimal('speech_pace_wpm', 6, 2)->nullable();
                $table->enum('speech_pace_rating', ['too_slow', 'slow', 'optimal', 'fast', 'too_fast'])->nullable();
                $table->json('filler_words')->nullable();
                $table->integer('filler_word_count')->default(0);
                $table->decimal('filler_word_percentage', 5, 2)->nullable();
                $table->json('pause_analysis')->nullable();
                $table->decimal('articulation_score', 5, 2)->nullable();
                $table->decimal('eye_contact_score', 5, 2)->nullable();
                $table->decimal('posture_score', 5, 2)->nullable();
                $table->decimal('gesture_score', 5, 2)->nullable();
                $table->decimal('facial_expression_score', 5, 2)->nullable();
                $table->json('body_language_breakdown')->nullable();
                $table->decimal('confidence_score', 5, 2)->nullable();
                $table->decimal('enthusiasm_score', 5, 2)->nullable();
                $table->decimal('nervousness_indicator', 5, 2)->nullable();
                $table->json('emotion_timeline')->nullable();
                $table->json('sentiment_analysis')->nullable();
                $table->decimal('overall_score', 5, 2)->nullable();
                $table->string('performance_grade')->nullable();
                $table->json('strengths')->nullable();
                $table->json('improvements')->nullable();
                $table->text('ai_summary')->nullable();
                $table->json('recommendations')->nullable();
                $table->timestamp('analysis_started_at')->nullable();
                $table->timestamp('analysis_completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('video_interview_invitations')) {
            Schema::create('video_interview_invitations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('video_interview_session_id')->constrained()->cascadeOnDelete();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
                $table->string('token', 64)->unique();
                $table->enum('status', ['pending', 'accepted', 'declined', 'expired', 'completed'])->default('pending');
                $table->text('message')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('accepted_at')->nullable();
                $table->timestamp('declined_at')->nullable();
                $table->timestamps();
                $table->index(['token']);
                $table->index(['candidate_id', 'status']);
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 6. COMPANY REVIEWS
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('company_reviews')) {
            Schema::create('company_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->integer('rating');
                $table->text('review_text');
                $table->string('position');
                $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship']);
                $table->text('pros')->nullable();
                $table->text('cons')->nullable();
                $table->text('advice_to_management')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->integer('helpful_count')->default(0);
                $table->timestamps();
                $table->unique(['user_id', 'company_id']);
                $table->index(['company_id', 'rating']);
                $table->index(['company_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('review_helpful')) {
            Schema::create('review_helpful', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')->constrained('company_reviews')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamp('created_at')->nullable();
                $table->unique(['review_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('company_user')) {
            Schema::create('company_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamp('created_at')->nullable();
                $table->unique(['company_id', 'user_id']);
                $table->index(['user_id', 'created_at']);
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 7. AGENT INTERNAL MATCHES
        // ─────────────────────────────────────────────────────────────────────

        if (! Schema::hasTable('agent_internal_matches')) {
            Schema::create('agent_internal_matches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
                $table->unsignedTinyInteger('match_score')->default(0);
                $table->json('score_breakdown')->nullable();
                $table->text('ai_reasoning')->nullable();
                $table->text('cover_letter')->nullable();
                $table->enum('status', ['pending', 'approved', 'skipped', 'applied'])->default('pending');
                $table->foreignId('application_id')->nullable()->constrained('applications')->nullOnDelete();
                $table->timestamp('applied_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'job_id']);
                $table->index(['user_id', 'status']);
                $table->index(['status', 'created_at']);
            });
        }

        // ─────────────────────────────────────────────────────────────────────
        // 8. COLUMN ADDITIONS TO EXISTING TABLES
        // ─────────────────────────────────────────────────────────────────────

        // applications — pipeline tracking
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'access_token')) {
                $table->string('access_token', 64)->nullable()->unique()->after('status');
            }
            if (! Schema::hasColumn('applications', 'hiring_stage')) {
                $table->string('hiring_stage')->nullable()->after('status');
            }
            if (! Schema::hasColumn('applications', 'pipeline_stage_date')) {
                $table->date('pipeline_stage_date')->nullable()->after('hiring_stage');
            }
            if (! Schema::hasColumn('applications', 'pipeline_stage_notes')) {
                $table->text('pipeline_stage_notes')->nullable()->after('pipeline_stage_date');
            }
            if (! Schema::hasColumn('applications', 'confirmation_email_sent')) {
                $table->boolean('confirmation_email_sent')->default(false)->after('pipeline_stage_notes');
            }
            if (! Schema::hasColumn('applications', 'test_link_token')) {
                $table->string('test_link_token', 64)->nullable()->unique()->after('confirmation_email_sent');
            }
        });

        // job_listings — Orin™ fields
        Schema::table('job_listings', function (Blueprint $table) {
            if (! Schema::hasColumn('job_listings', 'open_date')) {
                $table->date('open_date')->nullable()->after('expires_at');
            }
            if (! Schema::hasColumn('job_listings', 'close_date')) {
                $table->date('close_date')->nullable()->after('open_date');
            }
            if (! Schema::hasColumn('job_listings', 'eval_start_date')) {
                $table->date('eval_start_date')->nullable()->after('close_date');
            }
            if (! Schema::hasColumn('job_listings', 'final_date')) {
                $table->date('final_date')->nullable()->after('eval_start_date');
            }
            if (! Schema::hasColumn('job_listings', 'target_hire_count')) {
                $table->unsignedInteger('target_hire_count')->default(1)->after('final_date');
            }
            if (! Schema::hasColumn('job_listings', 'application_link_token')) {
                $table->string('application_link_token', 32)->nullable()->unique()->after('target_hire_count');
            }
            if (! Schema::hasColumn('job_listings', 'orin_generated_jd')) {
                $table->json('orin_generated_jd')->nullable()->after('application_link_token');
            }
            if (! Schema::hasColumn('job_listings', 'orin_application_form_fields')) {
                $table->json('orin_application_form_fields')->nullable()->after('orin_generated_jd');
            }
            if (! Schema::hasColumn('job_listings', 'application_phase')) {
                $table->enum('application_phase', [
                    'draft', 'open', 'closed', 'evaluating', 'ranked', 'complete',
                ])->default('draft')->after('orin_application_form_fields');
            }
            if (! Schema::hasColumn('job_listings', 'requires_portfolio')) {
                $table->boolean('requires_portfolio')->default(false)->after('application_phase');
            }
            if (! Schema::hasColumn('job_listings', 'requires_github')) {
                $table->boolean('requires_github')->default(false)->after('requires_portfolio');
            }
            if (! Schema::hasColumn('job_listings', 'requires_work_sample')) {
                $table->boolean('requires_work_sample')->default(false)->after('requires_github');
            }
            if (! Schema::hasColumn('job_listings', 'mandatory_screening_questions')) {
                $table->json('mandatory_screening_questions')->nullable()->after('requires_work_sample');
            }
        });

        // agent_configurations — pause fields
        if (Schema::hasTable('agent_configurations')) {
            Schema::table('agent_configurations', function (Blueprint $table) {
                if (! Schema::hasColumn('agent_configurations', 'is_paused')) {
                    $table->boolean('is_paused')->default(false)->after('is_active');
                }
                if (! Schema::hasColumn('agent_configurations', 'paused_at')) {
                    $table->timestamp('paused_at')->nullable()->after('is_paused');
                }
                if (! Schema::hasColumn('agent_configurations', 'pause_reason')) {
                    $table->string('pause_reason')->nullable()->after('paused_at');
                }
            });
        }
    }

    public function down(): void
    {
        // Column drops — applications
        Schema::table('applications', function (Blueprint $table) {
            $cols = array_filter(
                ['hiring_stage', 'pipeline_stage_date', 'pipeline_stage_notes', 'confirmation_email_sent', 'test_link_token', 'access_token'],
                fn ($c) => Schema::hasColumn('applications', $c)
            );
            if ($cols) {
                $table->dropColumn(array_values($cols));
            }
        });

        // Column drops — job_listings
        Schema::table('job_listings', function (Blueprint $table) {
            $cols = array_filter(
                ['open_date', 'close_date', 'eval_start_date', 'final_date', 'target_hire_count',
                 'application_link_token', 'orin_generated_jd', 'orin_application_form_fields',
                 'application_phase', 'requires_portfolio', 'requires_github',
                 'requires_work_sample', 'mandatory_screening_questions'],
                fn ($c) => Schema::hasColumn('job_listings', $c)
            );
            if ($cols) {
                $table->dropColumn(array_values($cols));
            }
        });

        Schema::dropIfExists('agent_internal_matches');
        Schema::dropIfExists('company_user');
        Schema::dropIfExists('review_helpful');
        Schema::dropIfExists('company_reviews');
        Schema::dropIfExists('video_interview_invitations');
        Schema::dropIfExists('video_interview_analyses');
        Schema::dropIfExists('video_interview_recordings');
        Schema::dropIfExists('video_interview_questions');
        Schema::dropIfExists('video_interview_sessions');
        Schema::dropIfExists('background_check_activities');
        Schema::dropIfExists('background_check_items');
        Schema::dropIfExists('background_checks');
        Schema::dropIfExists('background_check_packages');
        Schema::dropIfExists('interview_panel_scores');
        Schema::dropIfExists('interview_panelists');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('hiring_rounds');
        Schema::dropIfExists('hiring_test_attempts');
        Schema::dropIfExists('hiring_tests');
        Schema::dropIfExists('bulk_email_logs');
        Schema::dropIfExists('evaluation_answers');
        Schema::dropIfExists('evaluation_sessions');
        Schema::dropIfExists('question_banks');
    }
};
