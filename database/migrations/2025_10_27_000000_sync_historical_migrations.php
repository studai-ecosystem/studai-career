<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * PRODUCTION RECOVERY MIGRATION
 *
 * Problem: The `migrations` table only has 3 records (the 3 base Laravel migrations),
 * but the actual database has most tables already created. When `migrate --force` runs,
 * it tries to create `permissions` (migration #4) which already exists, causing a fatal
 * error that stops all subsequent migrations from running — including the repair migration
 * that creates the 4 genuinely missing tables.
 *
 * Fix: This migration detects the inconsistent state and fake-records all historical
 * migrations into the migrations table, unblocking the pipeline so the repair migration
 * (`2026_11_09_000001_repair_missing_core_tables`) can actually run.
 *
 * Safe on fresh installs: returns early if permissions table doesn't exist or is
 * already recorded (i.e., normal migration state).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Only act if we're in the inconsistent state:
        // permissions table physically exists but its migration isn't recorded.
        $permissionsRecorded = DB::table('migrations')
            ->where('migration', '2025_10_28_161526_create_permission_tables')
            ->exists();

        if ($permissionsRecorded || ! Schema::hasTable('permissions')) {
            // Either already recorded (normal state) or fresh install — do nothing.
            return;
        }

        // ── Inconsistent state detected ────────────────────────────────────────────
        // Fake-record all historical migrations so `migrate --force` stops trying
        // to re-create tables that already exist. The repair migration
        // (2026_11_09_000001_repair_missing_core_tables) is intentionally excluded
        // so it runs for real and creates the 4 missing tables.
        $alreadyRecorded = DB::table('migrations')->pluck('migration')->flip()->all();
        $nextBatch       = ((int) DB::table('migrations')->max('batch')) + 1;

        $historical = [
            '2025_10_28_161526_create_permission_tables',
            '2025_10_28_162737_add_studai_fields_to_users_table',
            '2025_10_28_162741_create_profiles_table',
            '2025_10_28_162746_create_companies_table',
            '2025_10_28_162750_create_industries_table',
            '2025_10_28_162758_create_company_industry_table',
            '2025_10_28_162800_create_job_listings_table',
            '2025_10_28_162805_create_skills_table',
            '2025_10_28_162808_create_applications_table',
            '2025_10_28_162814_create_subscription_plans_table',
            '2025_10_28_162820_create_user_subscriptions_table',
            '2025_10_28_162824_create_payment_transactions_table',
            '2025_10_28_162828_create_ai_conversations_table',
            '2025_10_28_162832_create_ai_usage_logs_table',
            '2025_10_28_162837_create_saved_jobs_table',
            '2025_10_28_162841_create_job_alerts_table',
            '2025_10_28_164319_create_personal_access_tokens_table',
            '2025_10_28_164353_add_two_factor_columns_to_users_table',
            '2025_10_28_172249_create_testimonials_table',
            '2025_10_28_172256_create_newsletters_table',
            '2025_10_28_172259_create_feature_flags_table',
            '2025_11_04_000000_create_autonomous_agent_tables',
            '2025_11_05_073524_create_interview_intelligence_tables',
            '2025_11_05_130000_create_negotiation_strategist_tables',
            '2025_11_06_000002_create_scout_corporate_dna_tables',
            '2025_11_06_000003_create_scout_assessment_tables',
            '2025_11_06_000004_create_scout_behavioral_assessment_tables',
            '2025_11_06_000005_create_continuous_learning_tables',
            '2025_11_06_000007_create_bias_elimination_tables',
            '2025_11_06_100000_create_skill_analyzer_tables',
            '2025_11_07_000008_create_predictive_analytics_tables',
            '2025_11_18_000900_add_missing_performance_indexes',
            '2025_11_18_174939_add_deleted_at_to_applications_table',
            '2025_11_18_175441_add_expires_at_to_job_listings_table',
            '2025_11_18_180037_add_soft_deletes_to_companies_and_job_listings',
            '2025_11_26_create_company_reviews_table',
            '2025_11_27_100001_enhance_company_reviews_table',
            '2025_11_27_100002_create_salary_reports_table',
            '2025_11_27_100003_create_interview_experiences_table',
            '2025_11_27_100004_add_review_stats_to_companies_table',
            '2025_11_27_200000_create_career_coach_tables',
            '2025_11_27_200000_create_video_interview_tables',
            '2025_11_27_210000_create_talent_marketplace_tables',
            '2025_11_27_220000_create_gamification_tables',
            '2025_11_27_300000_create_professional_networking_tables',
            '2025_11_27_400000_create_social_auth_tables',
            '2025_11_27_500000_create_calendar_tables',
            '2025_11_27_600000_create_ats_integration_tables',
            '2025_11_27_700000_create_enhanced_analytics_tables',
            '2025_11_27_800000_create_email_templates_tables',
            '2025_11_27_900000_create_offer_letter_tables',
            '2025_11_27_950000_create_background_check_tables',
            '2025_11_29_082159_add_company_id_to_users_table',
            '2025_11_29_094905_add_missing_columns_to_job_listings_table',
            '2025_11_29_095703_add_more_missing_columns_to_job_listings_table',
            '2026_01_15_000000_add_analytics_fields',
            '2026_01_16_000000_create_employer_portal_tables',
            '2026_01_17_000000_create_employer_features_tables',
            '2026_01_17_create_search_tables',
            '2026_01_18_000000_create_api_tokens_and_webhooks_tables',
            '2026_01_20_000000_create_audit_logs_table',
            '2026_01_21_000000_create_push_subscriptions_table',
            '2026_01_22_000000_create_resume_builder_tables',
            '2026_02_06_000001_create_idempotency_keys_table',
            '2026_02_06_000002_create_ai_prompts_table',
            '2026_02_06_000003_create_job_embeddings_table',
            '2026_02_06_000004_create_ai_golden_tests_table',
            '2026_02_06_000005_add_emergency_stop_to_agent_configurations',
            '2026_02_06_000006_create_agent_audit_logs_table',
            '2026_02_06_000007_create_gdpr_tables',
            '2026_02_06_000008_add_require_approval_to_agent_configurations',
            '2026_02_06_100000_add_grace_period_to_user_subscriptions',
            '2026_02_07_000001_add_stripe_customer_id_to_users_table',
            '2026_02_07_000001_create_scout_decision_traces_table',
            '2026_02_22_000001_create_payment_activity_logs_table',
            '2026_05_06_134757_create_notifications_table',
            '2026_05_06_155009_create_telescope_entries_table',
            '2026_05_11_000001_add_orin_fields_to_job_listings_table',
            '2026_05_11_000002_add_evaluation_fields_to_applications_table',
            '2026_05_11_000003_create_orin_evaluation_tables',
            '2026_05_11_000004_create_company_intelligence_profiles_table',
            '2026_05_11_085109_add_is_archived_to_applications_table',
            '2026_05_11_093807_add_missing_columns_to_applications',
            '2026_05_11_100000_fix_applications_status_constraint',
            '2026_05_11_140119_add_status_updated_at_and_rejection_reason_to_applications',
            '2026_05_12_000001_add_vantage_columns_to_interview_sessions',
            '2026_05_12_000002_create_coaching_skill_scores_table',
            '2026_05_12_000003_add_skill_scores_to_negotiation_sessions',
            '2026_05_12_000004_create_skill_badges_table',
            '2026_05_12_000005_create_vantage_prompt_templates_table',
            '2026_05_12_100000_add_contact_fields_to_companies_table',
            '2026_05_13_000001_add_ai_reason_to_applications_table',
            '2026_05_16_000001_create_interviews_pipeline_tables',
            '2026_05_16_083818_add_pipeline_columns_to_interviews_table',
            '2026_05_18_044006_create_ai_credit_logs_table',
            '2026_05_18_100000_add_hiring_pipeline_to_applications',
            '2026_05_18_110000_create_hiring_tests_tables',
            '2026_05_19_000001_create_hiring_rounds_table',
            '2026_05_19_054931_create_round_attempts_table',
            '2026_05_19_061718_add_violations_to_round_attempts_table',
            '2026_05_19_100000_create_agent_internal_matches_table',
            '2026_05_19_110000_add_pause_fields_to_agent_configurations',
            '2026_05_20_152055_create_freelancer_gigs_table',
            '2026_05_22_081832_create_cover_letters_table',
            '2026_05_22_144656_add_performance_indexes_to_job_listings_table',
            '2026_05_22_145931_add_missing_salary_fields_to_job_listings_table',
            '2026_05_22_150136_add_all_missing_columns_to_job_listings_table',
            '2026_05_22_150422_add_actual_outcome_to_negotiation_strategies_table',
            '2026_05_22_175141_add_ai_scoring_to_marketplace_proposals',
            '2026_05_23_115756_add_missing_columns_to_companies_table',
            '2026_05_23_200000_add_performance_indexes_phase2',
            '2026_05_25_063903_add_soft_deletes_to_payment_transactions_table',
            '2026_05_25_070558_add_career_goals_to_profiles_table',
            '2026_05_25_073111_add_cache_key_to_interview_sessions_table',
            '2026_05_25_073452_make_company_name_nullable_in_interview_sessions',
            '2026_05_25_090523_add_formality_level_to_negotiation_scripts_table',
            '2026_05_25_090559_make_full_script_nullable_in_negotiation_scripts',
            '2026_11_03_000012_create_assessments_tables',
            '2026_11_03_000013_create_subscription_tables',
            '2026_11_07_140000_create_talent_pipeline_tables',
            '2026_11_08_000001_seed_qa_test_accounts',
            // NOTE: 2026_11_09_000001_repair_missing_core_tables is intentionally
            // excluded — it must run for real to create the 4 missing tables.
        ];

        $rows = [];
        foreach ($historical as $migration) {
            if (! isset($alreadyRecorded[$migration])) {
                $rows[] = ['migration' => $migration, 'batch' => $nextBatch];
            }
        }

        if (! empty($rows)) {
            DB::table('migrations')->insert($rows);
        }
    }

    public function down(): void
    {
        // No rollback — this is a one-time production recovery.
    }
};
