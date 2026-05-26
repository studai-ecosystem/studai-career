<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interview_sessions', function (Blueprint $table) {
            $table->json('skill_map')->nullable()->after('ai_insights');
            $table->string('focus_skill')->nullable()->after('skill_map');
            $table->decimal('vantage_score', 3, 2)->nullable()->after('focus_skill');
            $table->timestamp('evaluator_ran_at')->nullable()->after('vantage_score');
        });
    }

    public function down(): void
    {
        Schema::table('interview_sessions', function (Blueprint $table) {
            $table->dropColumn(['skill_map', 'focus_skill', 'vantage_score', 'evaluator_ran_at']);
        });
    }
};
