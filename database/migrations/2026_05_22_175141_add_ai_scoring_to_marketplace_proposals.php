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
        Schema::table('marketplace_proposals', function (Blueprint $table) {
            $table->unsignedTinyInteger('ai_match_score')->nullable()->after('status');
            $table->json('ai_match_breakdown')->nullable()->after('ai_match_score');
            $table->timestamp('offer_sent_at')->nullable()->after('responded_at');
            $table->timestamp('offer_responded_at')->nullable()->after('offer_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_proposals', function (Blueprint $table) {
            $table->dropColumn(['ai_match_score', 'ai_match_breakdown', 'offer_sent_at', 'offer_responded_at']);
        });
    }
};
