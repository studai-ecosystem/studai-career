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
        Schema::create('round_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hiring_round_id')->constrained('hiring_rounds')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('applications')->nullOnDelete();
            $table->json('questions')->nullable();
            $table->json('answers')->nullable();
            $table->unsignedTinyInteger('score')->nullable();       // 0-100
            $table->text('ai_feedback')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'evaluated'])->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['hiring_round_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('round_attempts');
    }
};
