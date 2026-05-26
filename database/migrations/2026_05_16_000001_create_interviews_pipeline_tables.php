<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main interviews table
        if (!Schema::hasTable('interviews')) {
            Schema::create('interviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained()->onDelete('cascade');
                $table->string('interview_type')->default('video'); // phone, video, onsite, technical, behavioral, panel
                $table->dateTime('scheduled_at')->nullable();
                $table->integer('duration_minutes')->default(60);
                $table->string('location')->nullable();
                $table->string('meeting_link')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, canceled, no_show
                $table->integer('round')->default(1);
                $table->json('question_set')->nullable();       // Array of questions prepared
                $table->json('feedback')->nullable();           // Structured feedback
                $table->decimal('rating', 3, 1)->nullable();   // 1-5 overall rating
                $table->text('interviewer_notes')->nullable();
                $table->string('ai_recommendation')->nullable(); // hire, next_round, reject, silver_medal
                $table->json('ai_score_summary')->nullable();   // AI aggregated scores
                $table->dateTime('started_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->dateTime('canceled_at')->nullable();
                $table->string('cancellation_reason')->nullable();
                $table->timestamps();

                $table->index(['application_id', 'status']);
                $table->index('scheduled_at');
            });
        }

        // Panelists pivot
        if (!Schema::hasTable('interview_panelists')) {
            Schema::create('interview_panelists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('interview_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('is_lead')->default(false);
                $table->string('status')->default('invited'); // invited, confirmed, declined
                $table->timestamps();

                $table->unique(['interview_id', 'user_id']);
            });
        }

        // Panel scores per question per interviewer
        if (!Schema::hasTable('interview_panel_scores')) {
            Schema::create('interview_panel_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('interview_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // interviewer
                $table->string('question_key'); // slug/key from question_set
                $table->integer('score')->default(3);       // 1-5
                $table->text('comment')->nullable();
                $table->timestamps();

                $table->index(['interview_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_panel_scores');
        Schema::dropIfExists('interview_panelists');
        Schema::dropIfExists('interviews');
    }
};
