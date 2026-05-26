<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Evaluation state
            $table->enum('evaluation_status', [
                'pending', 'invited', 'in_progress', 'completed', 'expired', 'skipped'
            ])->default('pending')->after('status');

            // Orin™ scoring outputs
            $table->decimal('evaluation_score', 5, 2)->nullable()->after('evaluation_status');
            $table->decimal('skill_match_score', 5, 2)->nullable()->after('evaluation_score');
            $table->decimal('resume_quality_score', 5, 2)->nullable()->after('skill_match_score');
            $table->decimal('behavioural_fit_score', 5, 2)->nullable()->after('resume_quality_score');
            $table->decimal('final_rank_score', 5, 2)->nullable()->after('behavioural_fit_score');
            $table->unsignedInteger('rank_position')->nullable()->after('final_rank_score');

            // Evaluation completion tracking
            $table->timestamp('evaluation_started_at')->nullable()->after('rank_position');
            $table->timestamp('evaluation_completed_at')->nullable()->after('evaluation_started_at');

            // Notification tracking
            $table->boolean('application_email_sent')->default(false)->after('evaluation_completed_at');
            $table->boolean('evaluation_invite_sent')->default(false)->after('application_email_sent');
            $table->boolean('result_email_sent')->default(false)->after('evaluation_invite_sent');
            $table->timestamp('result_notified_at')->nullable()->after('result_email_sent');

            // Optional materials
            $table->string('portfolio_url')->nullable()->after('result_notified_at');
            $table->string('github_url')->nullable()->after('portfolio_url');
            $table->string('work_sample_url')->nullable()->after('github_url');
            $table->json('screening_answers')->nullable()->after('work_sample_url');

            // Application access token (for link-based access without full account)
            $table->string('access_token', 64)->nullable()->unique()->after('screening_answers');
            $table->boolean('is_guest_applicant')->default(false)->after('access_token');
            $table->string('guest_name')->nullable()->after('is_guest_applicant');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->string('guest_phone')->nullable()->after('guest_email');

            $table->index('evaluation_status');
            $table->index('final_rank_score');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['evaluation_status']);
            $table->dropIndex(['final_rank_score']);
            $table->dropColumn([
                'evaluation_status', 'evaluation_score', 'skill_match_score',
                'resume_quality_score', 'behavioural_fit_score', 'final_rank_score',
                'rank_position', 'evaluation_started_at', 'evaluation_completed_at',
                'application_email_sent', 'evaluation_invite_sent', 'result_email_sent',
                'result_notified_at', 'portfolio_url', 'github_url', 'work_sample_url',
                'screening_answers', 'access_token', 'is_guest_applicant',
                'guest_name', 'guest_email', 'guest_phone',
            ]);
        });
    }
};
