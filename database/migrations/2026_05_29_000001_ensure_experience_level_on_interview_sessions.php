<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('interview_sessions', 'experience_level')) {
            Schema::table('interview_sessions', function (Blueprint $table) {
                $table->string('experience_level')->nullable()->after('job_title');
            });
        }
    }

    public function down(): void
    {
        // intentionally left empty — do not drop a column added elsewhere
    }
};
