<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Responsible AI — XAI, Human Override, Audit Logs, Bias Detection, AI Disclaimers
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. AI Decision Logs — every AI decision recorded here ──────────────
        if (! Schema::hasTable('ai_decision_logs')) {
            Schema::create('ai_decision_logs', function (Blueprint $table): void {
                $table->id();
                // What entity was scored
                $table->string('subject_type');                    // Application, User, Resume, etc.
                $table->unsignedBigInteger('subject_id');
                // Who initiated the decision (employer/admin/system)
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('actor_type')->nullable();          // User, System
                // The decision
                $table->string('decision_type');                   // shortlist, reject, score, recommend, flag
                $table->string('model_used')->nullable();          // gpt-5.4, azure-gpt4, etc.
                $table->decimal('ai_score', 5, 2)->nullable();     // 0-100
                $table->string('ai_recommendation')->nullable();   // shortlist | reject | review | hold
                $table->decimal('confidence', 4, 3)->nullable();   // 0.000-1.000
                // Explanation (XAI)
                $table->json('score_factors')->nullable();         // [{factor, weight, value, contribution}]
                $table->json('evidence')->nullable();              // key evidence snippets
                $table->text('natural_language_explanation')->nullable();
                // Bias flags
                $table->boolean('bias_flagged')->default(false);
                $table->json('bias_indicators')->nullable();       // [{type, severity, detail}]
                // Context snapshot
                $table->json('input_context')->nullable();         // job_id, candidate snapshot, etc.
                $table->json('raw_ai_response')->nullable();
                $table->integer('processing_ms')->nullable();
                // Final decision (may differ from AI if human overrides)
                $table->string('final_decision')->nullable();
                $table->boolean('was_overridden')->default(false);
                $table->timestamps();

                $table->index(['subject_type', 'subject_id']);
                $table->index(['decision_type', 'created_at']);
                $table->index('bias_flagged');
                $table->index('actor_id');
            });
        }

        // ── 2. Human Overrides — any human correction of an AI decision ────────
        if (! Schema::hasTable('human_overrides')) {
            Schema::create('human_overrides', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('ai_decision_log_id')->nullable();
                // What entity was overridden
                $table->string('subject_type');
                $table->unsignedBigInteger('subject_id');
                // Who overrode
                $table->unsignedBigInteger('overrider_id');       // FK to users
                $table->string('overrider_role')->nullable();      // admin, employer, recruiter
                // What was changed
                $table->string('original_decision')->nullable();   // AI's original decision
                $table->decimal('original_score', 5, 2)->nullable();
                $table->string('override_decision');               // human's decision
                $table->decimal('override_score', 5, 2)->nullable();
                // Why
                $table->text('reason')->nullable();
                $table->string('override_category')->default('general');
                // e.g. 'bias_correction', 'additional_context', 'policy', 'error', 'general'
                $table->boolean('is_bias_correction')->default(false);
                $table->json('additional_context')->nullable();
                // Acknowledgment
                $table->boolean('requires_justification')->default(false);
                $table->text('justification')->nullable();
                $table->timestamp('acknowledged_at')->nullable();
                $table->timestamps();

                $table->index(['subject_type', 'subject_id']);
                $table->index('overrider_id');
                $table->index('is_bias_correction');
                $table->index('ai_decision_log_id');
            });
        }

        // ── 3. AI Bias Reports — aggregated bias analysis ──────────────────────
        if (! Schema::hasTable('ai_bias_reports')) {
            Schema::create('ai_bias_reports', function (Blueprint $table): void {
                $table->id();
                $table->string('report_type');                    // demographic, geographic, institutional, name_pattern, intersectional
                $table->string('scope');                          // company, job, global
                $table->unsignedBigInteger('scope_id')->nullable();
                $table->date('period_start');
                $table->date('period_end');
                // Analysis results
                $table->unsignedInteger('total_decisions_analysed')->default(0);
                $table->json('group_metrics')->nullable();         // [{group, avg_score, acceptance_rate, count}]
                $table->json('disparity_ratios')->nullable();      // statistical disparity metrics
                $table->decimal('bias_severity', 3, 2)->nullable(); // 0.00-1.00
                $table->string('bias_level')->nullable();          // none, low, moderate, high, critical
                $table->json('protected_attributes_affected')->nullable();
                $table->json('recommendations')->nullable();
                $table->boolean('requires_review')->default(false);
                $table->boolean('reviewed')->default(false);
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('review_notes')->nullable();
                $table->string('status')->default('pending');     // pending, reviewed, actioned, dismissed
                $table->timestamps();

                $table->index(['scope', 'scope_id']);
                $table->index(['bias_level', 'status']);
                $table->index('requires_review');
            });
        }

        // ── 4. AI Disclaimers — configurable disclaimer templates ──────────────
        if (! Schema::hasTable('ai_disclaimers')) {
            Schema::create('ai_disclaimers', function (Blueprint $table): void {
                $table->id();
                $table->string('key')->unique();                  // e.g. 'screening_result', 'rejection', 'shortlist_ai'
                $table->string('title');
                $table->text('body');
                $table->string('context');                        // where shown: employer_screening, candidate_result, admin, global
                $table->string('severity')->default('info');      // info, warning, critical
                $table->boolean('requires_acknowledgment')->default(false);
                $table->boolean('show_to_candidate')->default(false);
                $table->boolean('show_to_employer')->default(true);
                $table->boolean('show_to_admin')->default(true);
                $table->boolean('is_active')->default(true);
                $table->integer('display_order')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['context', 'is_active']);
                $table->index('key');
            });
        }

        // ── 5. AI Disclaimer Acknowledgments — who acknowledged what ──────────
        if (! Schema::hasTable('ai_disclaimer_acknowledgments')) {
            Schema::create('ai_disclaimer_acknowledgments', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('disclaimer_id');
                $table->unsignedBigInteger('user_id');
                $table->string('subject_type')->nullable();       // optional: what they were viewing
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamp('acknowledged_at');
                $table->timestamps();

                $table->index(['disclaimer_id', 'user_id']);
                $table->index('user_id');
                $table->unique(['disclaimer_id', 'user_id', 'subject_type', 'subject_id'], 'unique_disclaimer_ack');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_disclaimer_acknowledgments');
        Schema::dropIfExists('ai_disclaimers');
        Schema::dropIfExists('ai_bias_reports');
        Schema::dropIfExists('human_overrides');
        Schema::dropIfExists('ai_decision_logs');
    }
};
