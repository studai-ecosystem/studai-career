<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Stored AI-generated reason sent to the candidate on each status change
            $table->text('ai_reason')->nullable()->after('rejection_reason');
            // JSON snapshot of all status changes with timestamps + reasons for audit
            $table->json('status_history')->nullable()->after('ai_reason');
        });

        // Extend the status enum to include 'interviewed' and 'pending' (used by ATS)
        // SQLite-safe: only run ALTER ENUM on MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM(
                'draft','submitted','pending','viewed','reviewing',
                'shortlisted','interview_scheduled','interviewed',
                'offered','accepted','hired','rejected','withdrawn'
            ) NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['ai_reason', 'status_history']);
        });
    }
};
