<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * hire_outcomes — the reverse half of the unification loop.
 *
 * Captures every terminal employer decision (hired / rejected) alongside a
 * snapshot of the candidate-side composite scores that informed S.C.O.U.T.
 * ranking. This durable ground-truth dataset feeds:
 *   - candidate learning paths (what closed the gap on a hire / reject)
 *   - S.C.O.U.T. threshold calibration (offline / human-reviewed)
 *
 * Calibration is intentionally NOT applied automatically to live thresholds;
 * this table is the auditable input a scheduled or human-reviewed process uses.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hire_outcomes')) {
            return;
        }

        Schema::create('hire_outcomes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('job_id')->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();

            // Terminal outcome.
            $table->enum('outcome', ['hired', 'rejected']);

            // Snapshot of the composite inputs at decision time (0-100).
            $table->decimal('evaluation_score', 5, 2)->nullable();
            $table->decimal('skill_match_score', 5, 2)->nullable();
            $table->decimal('resume_quality_score', 5, 2)->nullable();
            $table->decimal('behavioural_fit_score', 5, 2)->nullable();
            $table->decimal('final_rank_score', 5, 2)->nullable();

            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->unique('application_id');
            $table->index(['company_id', 'outcome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hire_outcomes');
    }
};
