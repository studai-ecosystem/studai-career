<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix applications.status CHECK constraint to include the values
 * actually used by the employer ATS: pending, reviewing, hired.
 *
 * SQLite doesn't support ALTER COLUMN, so we rebuild the table via raw SQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        // For SQLite: rebuild the table with the corrected CHECK constraint.
        // For MySQL/PostgreSQL: a simple column change would suffice, but this
        // raw-SQL approach is portable.

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement("
                CREATE TABLE IF NOT EXISTS \"applications_new\" (
                    \"id\" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                    \"user_id\" integer NULL,
                    \"job_id\" integer NOT NULL,
                    \"application_number\" varchar NOT NULL,
                    \"cover_letter\" text NULL,
                    \"resume_file\" varchar NULL,
                    \"answers\" text NULL,
                    \"status\" varchar NOT NULL DEFAULT 'pending'
                        CHECK(\"status\" IN (
                            'draft','submitted','pending','viewed','reviewing',
                            'shortlisted','interview_scheduled','interviewed',
                            'offered','accepted','rejected','withdrawn','hired'
                        )),
                    \"evaluation_status\" varchar NOT NULL DEFAULT 'pending'
                        CHECK(\"evaluation_status\" IN (
                            'pending','invited','in_progress','completed','expired','skipped'
                        )),
                    \"evaluation_score\" numeric NULL,
                    \"skill_match_score\" numeric NULL,
                    \"resume_quality_score\" numeric NULL,
                    \"behavioural_fit_score\" numeric NULL,
                    \"final_rank_score\" numeric NULL,
                    \"rank_position\" integer NULL,
                    \"evaluation_started_at\" datetime NULL,
                    \"evaluation_completed_at\" datetime NULL,
                    \"result_notified_at\" datetime NULL,
                    \"application_email_sent\" tinyint(1) NOT NULL DEFAULT 0,
                    \"evaluation_invite_sent\" tinyint(1) NOT NULL DEFAULT 0,
                    \"result_email_sent\" tinyint(1) NOT NULL DEFAULT 0,
                    \"portfolio_url\" varchar NULL,
                    \"github_url\" varchar NULL,
                    \"work_sample_url\" varchar NULL,
                    \"screening_answers\" text NULL,
                    \"access_token\" varchar NULL,
                    \"is_guest_applicant\" tinyint(1) NOT NULL DEFAULT 0,
                    \"guest_name\" varchar NULL,
                    \"guest_email\" varchar NULL,
                    \"guest_phone\" varchar NULL,
                    \"match_score\" integer NULL,
                    \"match_analysis\" text NULL,
                    \"timeline\" text NULL,
                    \"notes\" text NULL,
                    \"submitted_at\" datetime NULL,
                    \"viewed_at\" datetime NULL,
                    \"deleted_at\" datetime NULL,
                    \"created_at\" datetime NULL,
                    \"updated_at\" datetime NULL,
                    FOREIGN KEY (\"user_id\") REFERENCES \"users\" (\"id\") ON DELETE CASCADE,
                    FOREIGN KEY (\"job_id\") REFERENCES \"job_listings\" (\"id\") ON DELETE CASCADE
                )
            ");

            // Copy all existing data — map old status values to new ones
            DB::statement("
                INSERT INTO \"applications_new\"
                SELECT
                    \"id\",
                    \"user_id\",
                    \"job_id\",
                    \"application_number\",
                    \"cover_letter\",
                    \"resume_file\",
                    \"answers\",
                    CASE
                        WHEN \"status\" IN ('draft','submitted') THEN 'pending'
                        WHEN \"status\" = 'viewed' THEN 'reviewing'
                        WHEN \"status\" = 'interview_scheduled' THEN 'shortlisted'
                        WHEN \"status\" = 'interviewed' THEN 'shortlisted'
                        WHEN \"status\" = 'offered' THEN 'hired'
                        WHEN \"status\" = 'accepted' THEN 'hired'
                        WHEN \"status\" = 'withdrawn' THEN 'rejected'
                        ELSE \"status\"
                    END,
                    COALESCE(\"evaluation_status\", 'pending'),
                    \"evaluation_score\",
                    \"skill_match_score\",
                    \"resume_quality_score\",
                    \"behavioural_fit_score\",
                    \"final_rank_score\",
                    \"rank_position\",
                    \"evaluation_started_at\",
                    \"evaluation_completed_at\",
                    \"result_notified_at\",
                    COALESCE(\"application_email_sent\", 0),
                    COALESCE(\"evaluation_invite_sent\", 0),
                    COALESCE(\"result_email_sent\", 0),
                    \"portfolio_url\",
                    \"github_url\",
                    \"work_sample_url\",
                    \"screening_answers\",
                    \"access_token\",
                    COALESCE(\"is_guest_applicant\", 0),
                    \"guest_name\",
                    \"guest_email\",
                    \"guest_phone\",
                    \"match_score\",
                    \"match_analysis\",
                    \"timeline\",
                    \"notes\",
                    \"submitted_at\",
                    \"viewed_at\",
                    \"deleted_at\",
                    \"created_at\",
                    \"updated_at\"
                FROM \"applications\"
            ");

            DB::statement('DROP TABLE "applications"');
            DB::statement('ALTER TABLE "applications_new" RENAME TO "applications"');

            // Recreate indexes
            DB::statement('CREATE INDEX "applications_user_id_status_index" ON "applications" ("user_id", "status")');
            DB::statement('CREATE INDEX "applications_job_id_status_index" ON "applications" ("job_id", "status")');
            DB::statement('CREATE UNIQUE INDEX "applications_application_number_unique" ON "applications" ("application_number")');
            DB::statement('CREATE INDEX "applications_evaluation_status_index" ON "applications" ("evaluation_status")');
            DB::statement('CREATE UNIQUE INDEX "applications_access_token_unique" ON "applications" ("access_token") WHERE "access_token" IS NOT NULL');

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            // MySQL / PostgreSQL — just modify the column
            DB::statement("
                ALTER TABLE applications MODIFY COLUMN status ENUM(
                    'draft','submitted','pending','viewed','reviewing',
                    'shortlisted','interview_scheduled','interviewed',
                    'offered','accepted','rejected','withdrawn','hired'
                ) NOT NULL DEFAULT 'pending'
            ");
        }
    }

    public function down(): void
    {
        // Irreversible data migration — old status values are gone
    }
};
