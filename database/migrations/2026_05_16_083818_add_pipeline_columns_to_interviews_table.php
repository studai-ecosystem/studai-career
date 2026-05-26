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
        Schema::table('interviews', function (Blueprint $table) {
            if (!Schema::hasColumn('interviews', 'round')) {
                $table->integer('round')->default(1)->after('status');
            }
            if (!Schema::hasColumn('interviews', 'question_set')) {
                $table->json('question_set')->nullable()->after('round');
            }
            if (!Schema::hasColumn('interviews', 'ai_recommendation')) {
                $table->string('ai_recommendation')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('interviews', 'ai_score_summary')) {
                $table->json('ai_score_summary')->nullable()->after('ai_recommendation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn(['round', 'question_set', 'ai_recommendation', 'ai_score_summary']);
        });
    }
};
