<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_intelligence_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->unique();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Orin™ conversation transcript
            $table->json('onboarding_conversation')->nullable();

            // Extracted intelligence
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable();  // micro/small/medium/large/enterprise
            $table->unsignedInteger('headcount')->nullable();
            $table->year('founded_year')->nullable();
            $table->string('cin_gst')->nullable();
            $table->string('website')->nullable();

            // Culture & work style
            $table->string('work_culture')->nullable();          // collaborative/autonomous/fast-paced/structured
            $table->string('work_mode_preference')->nullable();  // remote/hybrid/onsite
            $table->json('top_performer_traits')->nullable();
            $table->json('salary_bands')->nullable();            // {junior, mid, senior, lead} => {min, max}
            $table->string('compensation_philosophy')->nullable(); // competitive/below-market/equity-heavy/performance-linked
            $table->json('pain_points')->nullable();             // hiring pain points

            // Communication & compliance
            $table->string('preferred_candidate_communication')->nullable();
            $table->string('hiring_frequency')->nullable();      // one-time/seasonal/ongoing/bulk
            $table->json('compliance_requirements')->nullable();

            // Meta
            $table->boolean('onboarding_complete')->default(false);
            $table->unsignedTinyInteger('completeness_score')->default(0); // 0-100
            $table->timestamp('last_enriched_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_intelligence_profiles');
    }
};
