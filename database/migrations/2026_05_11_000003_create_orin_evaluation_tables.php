<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-job generated question bank
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->enum('difficulty', ['foundational', 'intermediate', 'advanced']);
            $table->enum('question_type', ['mcq', 'short_answer', 'scenario', 'code_snippet', 'case_study', 'video_response']);
            $table->string('topic')->nullable();
            $table->text('question_text');
            $table->json('options')->nullable();            // MCQ options array
            $table->text('correct_answer')->nullable();    // For auto-scored types
            $table->text('evaluation_rubric')->nullable(); // For open-ended types
            $table->unsignedInteger('time_limit_seconds')->default(120);
            $table->unsignedInteger('max_score')->default(10);
            $table->boolean('is_behavioural')->default(false);
            $table->boolean('is_culture_fit')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['job_id', 'difficulty', 'question_type']);
        });

        // Per-candidate evaluation session (Redis-backed for active, persisted here)
        Schema::create('evaluation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable(); // null for guest applicants
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // Session state
            $table->enum('status', ['not_started', 'in_progress', 'paused', 'completed', 'expired'])->default('not_started');
            $table->string('session_token', 64)->unique();
            $table->string('redis_key', 128)->nullable(); // Active Redis key for live session

            // Question assignment (ordered per candidate)
            $table->json('assigned_question_ids');       // Ordered array of question_bank IDs
            $table->unsignedInteger('current_question_index')->default(0);
            $table->unsignedInteger('total_questions');

            // Difficulty tracking (adaptive)
            $table->enum('current_difficulty', ['foundational', 'intermediate', 'advanced'])->default('foundational');
            $table->unsignedInteger('consecutive_correct')->default(0);
            $table->unsignedInteger('consecutive_incorrect')->default(0);

            // Anti-cheat metrics
            $table->unsignedInteger('tab_switch_count')->default(0);
            $table->unsignedInteger('focus_loss_count')->default(0);
            $table->json('time_anomalies')->nullable(); // Array of suspicious time events
            $table->boolean('flagged_for_review')->default(false);

            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Evaluation window deadline
            $table->unsignedInteger('total_time_seconds')->nullable();

            // Scoring
            $table->decimal('raw_score', 5, 2)->nullable();
            $table->decimal('weighted_score', 5, 2)->nullable();
            $table->json('section_scores')->nullable(); // Breakdown by section

            $table->timestamps();

            $table->index(['application_id', 'status']);
            $table->index(['job_id', 'status']);
        });

        // Individual answers per evaluation session
        Schema::create('evaluation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('question_banks')->cascadeOnDelete();
            $table->unsignedInteger('question_index');

            $table->text('answer_text')->nullable();
            $table->json('answer_options')->nullable(); // For MCQ (selected option IDs)
            $table->string('video_response_url')->nullable();

            // Scoring
            $table->decimal('score_awarded', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2)->default(10);
            $table->text('ai_feedback')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->boolean('is_auto_scored')->default(false);

            // Timing anti-cheat
            $table->unsignedInteger('time_taken_seconds')->nullable();
            $table->boolean('time_anomaly')->default(false);

            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->index(['evaluation_session_id', 'question_index']);
        });

        // Bulk email job tracking
        Schema::create('bulk_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->enum('email_type', [
                'application_received', 'evaluation_open', 'shortlisted',
                'rejected', 'evaluation_reminder', 'results_ready'
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

    public function down(): void
    {
        Schema::dropIfExists('bulk_email_logs');
        Schema::dropIfExists('evaluation_answers');
        Schema::dropIfExists('evaluation_sessions');
        Schema::dropIfExists('question_banks');
    }
};
