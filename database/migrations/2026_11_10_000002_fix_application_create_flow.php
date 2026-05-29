<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCTION FIX MIGRATION — Application Create Flow
 *
 * Fixes the 500 error when job seekers try to apply for a job.
 *
 * Root causes addressed:
 *   1. `round_attempts` table is only in the sync migration list — never actually
 *      created on inconsistent installs. JobController::show() eagerly loads
 *      hiringRounds and then queries round_attempts — throws if table missing.
 *   2. `applications.is_archived` and `applications.source` columns may be absent
 *      on inconsistent installs (migrations were in sync list, marked done without run).
 *   3. `applications.status` enum may not contain 'submitted' on old installs
 *      (original enum only had: draft, submitted, viewed, shortlisted,
 *       interview_scheduled, interviewed, offered, accepted, rejected, withdrawn).
 *      This migration ensures 'submitted' and common statuses are present.
 *   4. `application_number` column may be missing on old installs.
 *
 * All blocks guarded by Schema::hasTable / Schema::hasColumn — safe to run
 * multiple times and on fresh installs.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. round_attempts — only created by sync-listed migration ────────
        if (! Schema::hasTable('round_attempts')) {
            Schema::create('round_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('hiring_round_id')->constrained()->cascadeOnDelete();
                $table->foreignId('application_id')->nullable()->constrained('applications')->nullOnDelete();
                $table->string('status')->default('pending'); // pending, in_progress, completed, expired, disqualified
                $table->integer('score')->nullable();
                $table->integer('max_score')->nullable();
                $table->decimal('percentage', 5, 2)->nullable();
                $table->boolean('passed')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->integer('time_taken_seconds')->nullable();
                $table->json('answers')->nullable();
                $table->json('violations')->nullable();
                $table->integer('violation_count')->default(0);
                $table->boolean('is_disqualified')->default(false);
                $table->text('disqualification_reason')->nullable();
                $table->text('ai_feedback')->nullable();
                $table->json('ai_analysis')->nullable();
                $table->string('access_token', 64)->nullable()->unique();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['user_id', 'hiring_round_id']);
                $table->index(['hiring_round_id', 'status']);
                $table->index(['user_id', 'status']);
            });
        }

        // ── 2. applications — ensure all columns used by JobController::apply() ─
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (! Schema::hasColumn('applications', 'application_number')) {
                    $table->string('application_number')->nullable()->after('user_id');
                }

                if (! Schema::hasColumn('applications', 'submitted_at')) {
                    $table->timestamp('submitted_at')->nullable()->after('status');
                }

                if (! Schema::hasColumn('applications', 'is_archived')) {
                    $table->boolean('is_archived')->default(false)->after('submitted_at');
                }

                if (! Schema::hasColumn('applications', 'source')) {
                    $table->string('source')->nullable()->after('is_archived');
                }

                if (! Schema::hasColumn('applications', 'status_updated_at')) {
                    $table->timestamp('status_updated_at')->nullable()->after('status');
                }

                if (! Schema::hasColumn('applications', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('status_updated_at');
                }
            });

            // Ensure unique constraint on application_number exists (add only if the
            // column was just added as nullable — skip if the constraint already exists)
            try {
                $hasIndex = collect(DB::select("SHOW INDEX FROM applications WHERE Key_name = 'applications_application_number_unique'"))->isNotEmpty();
                if (! $hasIndex && Schema::hasColumn('applications', 'application_number')) {
                    Schema::table('applications', function (Blueprint $table) {
                        // Make nullable so existing rows with no value don't conflict
                        $table->string('application_number')->nullable()->unique()->change();
                    });
                }
            } catch (\Throwable) {
                // Index check is best-effort — ignore on non-MySQL
            }

            // ── Fix status enum to include 'submitted' (guaranteed in original schema) ─
            // On very old installs the enum may only have draft/viewed/shortlisted etc.
            // We add the missing values. Using raw ALTER because Schema Builder requires
            // Doctrine DBAL which isn't always available and enum change is simple here.
            try {
                $dbName = DB::connection()->getDatabaseName();
                $row = DB::selectOne(
                    "SELECT COLUMN_TYPE FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'applications' AND COLUMN_NAME = 'status'",
                    [$dbName]
                );

                if ($row) {
                    $currentType = $row->COLUMN_TYPE ?? '';
                    $required = ['draft', 'submitted', 'pending', 'viewed', 'reviewing',
                        'shortlisted', 'interview_scheduled', 'interviewed',
                        'offered', 'accepted', 'rejected', 'withdrawn', 'hired'];

                    $needsUpdate = false;
                    foreach ($required as $val) {
                        if (! str_contains($currentType, "'$val'")) {
                            $needsUpdate = true;
                            break;
                        }
                    }

                    if ($needsUpdate) {
                        $enumList = implode(',', array_map(fn($v) => "'$v'", $required));
                        DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM($enumList) NOT NULL DEFAULT 'draft'");
                    }
                }
            } catch (\Throwable $e) {
                // Enum fix is best-effort — log but don't fail the migration
                \Illuminate\Support\Facades\Log::warning('Could not fix applications.status enum: ' . $e->getMessage());
            }
        }

        // ── 3. hiring_rounds — created by deploy_screening_pipeline but guard anyway ─
        if (! Schema::hasTable('hiring_rounds')) {
            Schema::create('hiring_rounds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
                $table->string('name');
                $table->string('type')->default('test'); // test, interview, document, task
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('round_order')->default(1);
                $table->boolean('is_mandatory')->default(true);
                $table->boolean('is_active')->default(true);
                $table->integer('time_limit_minutes')->nullable();
                $table->integer('passing_score')->nullable();
                $table->json('instructions')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();
                $table->index(['job_id', 'round_order']);
            });
        }
    }

    public function down(): void
    {
        // Intentionally left bare — data-only migration
    }
};
