<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hiring tests — company creates one test per job+stage
        Schema::create('hiring_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            $table->string('stage', 50); // company_info_test | aptitude | one_on_one
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->json('questions'); // [{question, options:[],correct_index,marks}]
            $table->unsignedInteger('pass_score')->default(60); // percentage
            $table->unsignedInteger('time_limit_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['job_id', 'stage']); // one test per job per stage
        });

        // Candidate test attempts
        Schema::create('hiring_test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hiring_test_id')->constrained()->cascadeOnDelete();
            $table->string('stage', 50);
            $table->json('answers')->nullable();  // [question_index => selected_index]
            $table->unsignedInteger('score')->nullable();    // percentage
            $table->boolean('passed')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['application_id', 'stage']); // one attempt per stage
        });

        // Add test_link_token to applications for secure candidate access
        Schema::table('applications', function (Blueprint $table) {
            $table->string('test_link_token', 64)->nullable()->unique()->after('access_token');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('test_link_token');
        });
        Schema::dropIfExists('hiring_test_attempts');
        Schema::dropIfExists('hiring_tests');
    }
};
