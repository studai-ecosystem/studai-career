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
        Schema::create('ai_credit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action', 100); // cover_letter, resume_review, interview_prep, ai_apply, skill_analysis, etc.
            $table->string('description');  // Human readable: "AI Cover Letter for Data Analyst at Google"
            $table->unsignedInteger('credits_used')->default(1);
            $table->json('meta')->nullable(); // job_id, job_title, company, etc.
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credit_logs');
    }
};
