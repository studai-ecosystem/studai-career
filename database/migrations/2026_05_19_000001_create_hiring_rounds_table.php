<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hiring_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->string('name');                  // e.g. "Company Info Test", "Aptitude"
            $table->enum('type', [
                'info_test', 'aptitude', 'technical', 'hr_interview',
                'culture_fit', 'practical', 'portfolio_review',
            ]);
            $table->unsignedTinyInteger('round_order')->default(1);
            $table->text('description')->nullable();
            $table->text('ai_evaluation_criteria')->nullable(); // AI uses this to evaluate
            $table->date('test_date')->nullable();
            $table->unsignedTinyInteger('evaluation_days')->default(5); // 5 or 10
            $table->date('evaluation_date')->nullable();                // test_date + evaluation_days
            $table->enum('status', ['pending', 'active', 'evaluating', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hiring_rounds');
    }
};
