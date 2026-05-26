<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            // Orin™ application lifecycle dates
            $table->date('open_date')->nullable()->after('expires_at');
            $table->date('close_date')->nullable()->after('open_date');
            $table->date('eval_start_date')->nullable()->after('close_date');
            $table->date('final_date')->nullable()->after('eval_start_date');
            $table->unsignedInteger('target_hire_count')->default(1)->after('final_date');

            // Shareable application link token (career.studai.one/apply/{token})
            $table->string('application_link_token', 32)->nullable()->unique()->after('target_hire_count');

            // Orin™ generated content
            $table->json('orin_generated_jd')->nullable()->after('application_link_token');
            $table->json('orin_application_form_fields')->nullable()->after('orin_generated_jd');

            // Application phase tracking
            $table->enum('application_phase', [
                'draft', 'open', 'closed', 'evaluating', 'ranked', 'complete'
            ])->default('draft')->after('orin_application_form_fields');

            // Portfolio / work sample requirements
            $table->boolean('requires_portfolio')->default(false)->after('application_phase');
            $table->boolean('requires_github')->default(false)->after('requires_portfolio');
            $table->boolean('requires_work_sample')->default(false)->after('requires_github');

            // Employer-mandated screening questions (JSON array)
            $table->json('mandatory_screening_questions')->nullable()->after('requires_work_sample');

            // Index on date columns for scheduled command performance
            $table->index(['close_date', 'application_phase']);
            $table->index(['eval_start_date', 'application_phase']);
            $table->index(['final_date', 'application_phase']);
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropIndex(['close_date', 'application_phase']);
            $table->dropIndex(['eval_start_date', 'application_phase']);
            $table->dropIndex(['final_date', 'application_phase']);
            $table->dropColumn([
                'open_date', 'close_date', 'eval_start_date', 'final_date',
                'target_hire_count', 'application_link_token',
                'orin_generated_jd', 'orin_application_form_fields',
                'application_phase', 'requires_portfolio', 'requires_github',
                'requires_work_sample', 'mandatory_screening_questions',
            ]);
        });
    }
};
