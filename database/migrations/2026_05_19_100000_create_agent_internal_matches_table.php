<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_internal_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->unsignedTinyInteger('match_score')->default(0); // 0-100
            $table->json('score_breakdown')->nullable();            // per-dimension scores
            $table->text('ai_reasoning')->nullable();               // why it matched
            $table->text('cover_letter')->nullable();               // AI-generated cover letter
            $table->enum('status', ['pending', 'approved', 'skipped', 'applied'])
                  ->default('pending');
            $table->foreignId('application_id')->nullable()
                  ->constrained('applications')->nullOnDelete();
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'job_id']); // no duplicate matches
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_internal_matches');
    }
};
