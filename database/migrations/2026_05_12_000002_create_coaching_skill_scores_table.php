<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaching_skill_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained('career_coach_sessions')->onDelete('cascade');
            $table->string('skill', 50); // critical_thinking, collaboration, communication, creativity, adaptability
            $table->decimal('score', 3, 2); // 1.00 – 5.00
            $table->json('sub_scores')->nullable(); // per sub-skill breakdown
            $table->string('level', 30)->nullable(); // Not Demonstrated / Emerging / Developing / Proficient / Advanced
            $table->json('evidence_quotes')->nullable();
            $table->text('improvement_tips')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'skill']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_skill_scores');
    }
};
