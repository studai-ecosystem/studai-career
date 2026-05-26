<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('job_listings', 'posted_by')) {
                $table->unsignedBigInteger('posted_by')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('job_listings', 'location_type')) {
                $table->string('location_type', 50)->nullable()->after('location');
            }
            if (!Schema::hasColumn('job_listings', 'job_type')) {
                $table->string('job_type', 50)->nullable()->after('employment_type');
            }
            if (!Schema::hasColumn('job_listings', 'preferred_skills')) {
                $table->json('preferred_skills')->nullable()->after('required_skills');
            }
            if (!Schema::hasColumn('job_listings', 'application_method')) {
                $table->string('application_method', 30)->default('platform')->after('benefits');
            }
            if (!Schema::hasColumn('job_listings', 'external_url')) {
                $table->string('external_url')->nullable()->after('application_method');
            }
            if (!Schema::hasColumn('job_listings', 'application_email')) {
                $table->string('application_email')->nullable()->after('external_url');
            }
            if (!Schema::hasColumn('job_listings', 'application_instructions')) {
                $table->text('application_instructions')->nullable()->after('application_email');
            }
            if (!Schema::hasColumn('job_listings', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            if (!Schema::hasColumn('job_listings', 'is_urgent')) {
                $table->boolean('is_urgent')->default(false)->after('is_featured');
            }
            if (!Schema::hasColumn('job_listings', 'filled_at')) {
                $table->timestamp('filled_at')->nullable()->after('published_at');
            }
            if (!Schema::hasColumn('job_listings', 'views_count')) {
                $table->unsignedInteger('views_count')->default(0)->after('filled_at');
            }
            if (!Schema::hasColumn('job_listings', 'applications_count')) {
                $table->unsignedInteger('applications_count')->default(0)->after('views_count');
            }
            if (!Schema::hasColumn('job_listings', 'saves_count')) {
                $table->unsignedInteger('saves_count')->default(0)->after('applications_count');
            }
            if (!Schema::hasColumn('job_listings', 'search_keywords')) {
                $table->text('search_keywords')->nullable()->after('saves_count');
            }
            if (!Schema::hasColumn('job_listings', 'ai_embeddings')) {
                $table->json('ai_embeddings')->nullable()->after('search_keywords');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $columns = [
                'posted_by', 'location_type', 'job_type', 'preferred_skills',
                'application_method', 'external_url', 'application_email',
                'application_instructions', 'is_featured', 'is_urgent',
                'filled_at', 'views_count', 'applications_count', 'saves_count',
                'search_keywords', 'ai_embeddings',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('job_listings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
