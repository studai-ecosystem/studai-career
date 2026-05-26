<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Helper: only add index if it doesn't exist
        $hasIndex = fn(string $table, string $index): bool => collect(DB::select("PRAGMA index_list({$table})"))->contains('name', $index);

        // applications table — individual FKs for fast joins
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) use ($hasIndex) {
                if (! $hasIndex('applications', 'applications_job_id_index')) {
                    $table->index('job_id', 'applications_job_id_index');
                }
                if (! $hasIndex('applications', 'applications_user_id_index')) {
                    $table->index('user_id', 'applications_user_id_index');
                }
                if (! $hasIndex('applications', 'applications_status_job_id_index')) {
                    $table->index(['status', 'job_id'], 'applications_status_job_id_index');
                }
                if (! $hasIndex('applications', 'applications_company_status_index')) {
                    // Partial denorm index: job_id + status for employer dashboard counts
                    $table->index(['job_id', 'status', 'created_at'], 'applications_company_status_index');
                }
            });
        }

        // job_listings — company_id is the most common filter
        if (Schema::hasTable('job_listings')) {
            Schema::table('job_listings', function (Blueprint $table) use ($hasIndex) {
                if (! $hasIndex('job_listings', 'jobs_company_status_expires_index')) {
                    $table->index(['company_id', 'status', 'expires_at'], 'jobs_company_status_expires_index');
                }
                if (! $hasIndex('job_listings', 'jobs_company_created_index')) {
                    $table->index(['company_id', 'created_at'], 'jobs_company_created_index');
                }
            });
        }

        // notifications — for fast unread count on both sides
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) use ($hasIndex) {
                if (! $hasIndex('notifications', 'notifications_notifiable_read_index')) {
                    $table->index(['notifiable_id', 'notifiable_type', 'read_at'], 'notifications_notifiable_read_index');
                }
                if (! $hasIndex('notifications', 'notifications_created_at_index')) {
                    $table->index('created_at', 'notifications_created_at_index');
                }
            });
        }

        // interviews — employer schedule queries
        if (Schema::hasTable('interviews')) {
            Schema::table('interviews', function (Blueprint $table) use ($hasIndex) {
                if (! $hasIndex('interviews', 'interviews_company_status_index')) {
                    $table->index(['status', 'scheduled_at'], 'interviews_company_status_index');
                }
            });
        }

        // talent_pool — employer talent pool queries
        if (Schema::hasTable('talent_pool')) {
            Schema::table('talent_pool', function (Blueprint $table) use ($hasIndex) {
                if (! $hasIndex('talent_pool', 'talent_pool_company_active_index')) {
                    $table->index(['company_id', 'is_active'], 'talent_pool_company_active_index');
                }
            });
        }

        // user_profiles — profile lookups
        if (Schema::hasTable('user_profiles')) {
            Schema::table('user_profiles', function (Blueprint $table) use ($hasIndex) {
                if (! $hasIndex('user_profiles', 'user_profiles_user_id_index')) {
                    $table->index('user_id', 'user_profiles_user_id_index');
                }
            });
        }
    }

    public function down(): void
    {
        $safeDropIndex = function (string $table, string $index): void {
            try {
                Schema::table($table, fn(Blueprint $t) => $t->dropIndex($index));
            } catch (\Exception) {}
        };

        $safeDropIndex('applications', 'applications_job_id_index');
        $safeDropIndex('applications', 'applications_user_id_index');
        $safeDropIndex('applications', 'applications_status_job_id_index');
        $safeDropIndex('applications', 'applications_company_status_index');
        $safeDropIndex('job_listings', 'jobs_company_status_expires_index');
        $safeDropIndex('job_listings', 'jobs_company_created_index');
        $safeDropIndex('notifications', 'notifications_notifiable_read_index');
        $safeDropIndex('notifications', 'notifications_created_at_index');
        $safeDropIndex('interviews', 'interviews_company_status_index');
        $safeDropIndex('talent_pool', 'talent_pool_company_active_index');
        $safeDropIndex('user_profiles', 'user_profiles_user_id_index');
    }
};
