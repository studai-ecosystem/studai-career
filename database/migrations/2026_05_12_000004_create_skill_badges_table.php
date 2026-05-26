<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vantage_skill_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('skill', 50); // critical_thinking, collaboration, communication, creativity, adaptability
            $table->string('tier', 20); // emerging, developing, proficient, advanced
            $table->decimal('score', 3, 2);
            $table->string('source_type', 30); // interview_session, coaching_session, negotiation_session
            $table->unsignedBigInteger('source_id');
            $table->timestamp('unlocked_at');
            $table->timestamps();

            $table->unique(['user_id', 'skill', 'tier']); // one award per tier per skill per user
            $table->index(['user_id', 'skill']);
        });

        // Add composite vantage score to users
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('vantage_score', 3, 2)->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vantage_skill_awards');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('vantage_score');
        });
    }
};
