<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * F13: Track the attempt number for each Orin™ evaluation session so the
 * retake policy (config: ai.evaluation.retake) can enforce max_attempts and
 * audit retakes.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('evaluation_sessions', 'attempt_number')) {
            Schema::table('evaluation_sessions', function (Blueprint $table) {
                $table->unsignedTinyInteger('attempt_number')->default(1)->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('evaluation_sessions', 'attempt_number')) {
            Schema::table('evaluation_sessions', function (Blueprint $table) {
                $table->dropColumn('attempt_number');
            });
        }
    }
};
