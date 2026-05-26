<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negotiation_sessions', function (Blueprint $table) {
            $table->json('skill_scores')->nullable()->after('lessons_learned');
        });
    }

    public function down(): void
    {
        Schema::table('negotiation_sessions', function (Blueprint $table) {
            $table->dropColumn('skill_scores');
        });
    }
};
