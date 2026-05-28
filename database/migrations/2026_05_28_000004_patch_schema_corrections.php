<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SCHEMA PATCH — 2026-05-28 (Build #4 / Deploy #5)
 *
 * Fixes tables created by the initial c233cfb build of 000003 which had
 * incorrect schemas. Every block is guarded with hasColumn / hasTable so
 * this migration is fully idempotent regardless of which version of 000003
 * actually ran on production.
 *
 * Tables patched:
 *  - interview_sessions  : add 7 columns, make 2 nullable, drop junk column, add unique index
 *  - agent_configurations: add 9 columns, drop wrong boolean
 *  - negotiation_scripts : add includes_data, fix formality_level type
 *  - marketplace_proposals: add 4 columns
 */
return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════════════════
        // 1. interview_sessions
        // ═══════════════════════════════════════════════════════════════════

        if (Schema::hasTable('interview_sessions')) {
            // Drop junk column
            if (Schema::hasColumn('interview_sessions', 'company_name_nullable')) {
                Schema::table('interview_sessions', function (Blueprint $table) {
                    $table->dropColumn('company_name_nullable');
                });
            }

            // Make company_name and role_title nullable if they aren't already
            if (Schema::hasColumn('interview_sessions', 'company_name')) {
                Schema::table('interview_sessions', function (Blueprint $table) {
                    $table->string('company_name')->nullable()->change();
                });
            }
            if (Schema::hasColumn('interview_sessions', 'role_title')) {
                Schema::table('interview_sessions', function (Blueprint $table) {
                    $table->string('role_title')->nullable()->change();
                });
            }

            // Add missing columns
            Schema::table('interview_sessions', function (Blueprint $table) {
                if (! Schema::hasColumn('interview_sessions', 'job_title')) {
                    $table->string('job_title')->nullable()->after('role_title');
                }
                if (! Schema::hasColumn('interview_sessions', 'session_data')) {
                    $table->json('session_data')->nullable()->after('ai_insights');
                }
                if (! Schema::hasColumn('interview_sessions', 'skill_map')) {
                    $table->json('skill_map')->nullable()->after('interviewer_style');
                }
                if (! Schema::hasColumn('interview_sessions', 'focus_skill')) {
                    $table->string('focus_skill')->nullable()->after('skill_map');
                }
                if (! Schema::hasColumn('interview_sessions', 'vantage_score')) {
                    $table->decimal('vantage_score', 3, 2)->nullable()->after('focus_skill');
                }
                if (! Schema::hasColumn('interview_sessions', 'evaluator_ran_at')) {
                    $table->timestamp('evaluator_ran_at')->nullable()->after('vantage_score');
                }
            });

            // Add unique index to cache_key if missing
            $hasUniqueIndex = count(DB::select(
                "SHOW INDEX FROM interview_sessions WHERE Column_name = 'cache_key' AND Non_unique = 0"
            )) > 0;
            if (! $hasUniqueIndex && Schema::hasColumn('interview_sessions', 'cache_key')) {
                DB::statement('CREATE UNIQUE INDEX interview_sessions_cache_key_unique ON interview_sessions (cache_key)');
            }
        }

        // ═══════════════════════════════════════════════════════════════════
        // 2. agent_configurations
        // ═══════════════════════════════════════════════════════════════════

        if (Schema::hasTable('agent_configurations')) {
            // Drop wrong boolean column
            if (Schema::hasColumn('agent_configurations', 'emergency_stop')) {
                Schema::table('agent_configurations', function (Blueprint $table) {
                    $table->dropColumn('emergency_stop');
                });
            }

            // Add missing columns
            Schema::table('agent_configurations', function (Blueprint $table) {
                if (! Schema::hasColumn('agent_configurations', 'applications_today')) {
                    $table->integer('applications_today')->default(0)->after('applications_this_month');
                }
                if (! Schema::hasColumn('agent_configurations', 'applications_today_date')) {
                    $table->date('applications_today_date')->nullable()->after('applications_today');
                }
                if (! Schema::hasColumn('agent_configurations', 'approval_threshold')) {
                    $table->integer('approval_threshold')->default(80)->after('require_approval');
                }
                if (! Schema::hasColumn('agent_configurations', 'activated_at')) {
                    $table->timestamp('activated_at')->nullable()->after('last_run_at');
                }
                if (! Schema::hasColumn('agent_configurations', 'deactivated_at')) {
                    $table->timestamp('deactivated_at')->nullable()->after('activated_at');
                }
                if (! Schema::hasColumn('agent_configurations', 'emergency_stopped_at')) {
                    $table->timestamp('emergency_stopped_at')->nullable();
                }
                if (! Schema::hasColumn('agent_configurations', 'emergency_stopped_by')) {
                    $table->unsignedBigInteger('emergency_stopped_by')->nullable();
                }
                if (! Schema::hasColumn('agent_configurations', 'emergency_stop_reason')) {
                    $table->text('emergency_stop_reason')->nullable();
                }
                if (! Schema::hasColumn('agent_configurations', 'is_globally_stopped')) {
                    $table->boolean('is_globally_stopped')->default(false);
                }
            });

            // Add missing indices if needed
            $hasEmergencyStopIndex = count(DB::select(
                "SHOW INDEX FROM agent_configurations WHERE Key_name = 'agent_configurations_emergency_stopped_at_index'"
            )) > 0;
            if (! $hasEmergencyStopIndex && Schema::hasColumn('agent_configurations', 'emergency_stopped_at')) {
                DB::statement('CREATE INDEX agent_configurations_emergency_stopped_at_index ON agent_configurations (emergency_stopped_at)');
            }

            $hasGloballyStoppedIndex = count(DB::select(
                "SHOW INDEX FROM agent_configurations WHERE Key_name = 'agent_configurations_is_globally_stopped_index'"
            )) > 0;
            if (! $hasGloballyStoppedIndex && Schema::hasColumn('agent_configurations', 'is_globally_stopped')) {
                DB::statement('CREATE INDEX agent_configurations_is_globally_stopped_index ON agent_configurations (is_globally_stopped)');
            }
        }

        // ═══════════════════════════════════════════════════════════════════
        // 3. negotiation_scripts
        // ═══════════════════════════════════════════════════════════════════

        if (Schema::hasTable('negotiation_scripts')) {
            Schema::table('negotiation_scripts', function (Blueprint $table) {
                // Change formality_level from integer to string (idempotent)
                if (Schema::hasColumn('negotiation_scripts', 'formality_level')) {
                    $table->string('formality_level')->nullable()->change();
                }
                // Add includes_data if missing
                if (! Schema::hasColumn('negotiation_scripts', 'includes_data')) {
                    $table->boolean('includes_data')->default(false)->after('includes_alternatives');
                }
            });
        }

        // ═══════════════════════════════════════════════════════════════════
        // 4. marketplace_proposals
        // ═══════════════════════════════════════════════════════════════════

        if (Schema::hasTable('marketplace_proposals')) {
            Schema::table('marketplace_proposals', function (Blueprint $table) {
                if (! Schema::hasColumn('marketplace_proposals', 'ai_match_score')) {
                    $table->unsignedTinyInteger('ai_match_score')->nullable()->after('status');
                }
                if (! Schema::hasColumn('marketplace_proposals', 'ai_match_breakdown')) {
                    $table->json('ai_match_breakdown')->nullable()->after('ai_match_score');
                }
                if (! Schema::hasColumn('marketplace_proposals', 'offer_sent_at')) {
                    $table->timestamp('offer_sent_at')->nullable();
                }
                if (! Schema::hasColumn('marketplace_proposals', 'offer_responded_at')) {
                    $table->timestamp('offer_responded_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Intentionally a no-op — these are additive schema corrections
        // and reverting them would be destructive to production data.
    }
};
