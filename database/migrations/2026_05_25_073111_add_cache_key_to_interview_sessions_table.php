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
        Schema::table('interview_sessions', function (Blueprint $table) {
            $table->string('cache_key')->nullable()->unique()->after('id');
            $table->string('job_title')->nullable()->after('cache_key');
            $table->json('session_data')->nullable()->after('ai_insights');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_sessions', function (Blueprint $table) {
            $table->dropColumn(['cache_key', 'job_title', 'session_data']);
        });
    }
};
