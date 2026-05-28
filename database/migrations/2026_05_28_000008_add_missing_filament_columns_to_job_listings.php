<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('job_listings', 'nice_to_have')) {
                $table->json('nice_to_have')->nullable()->after('responsibilities');
            }
            if (!Schema::hasColumn('job_listings', 'ai_insights')) {
                $table->json('ai_insights')->nullable()->after('nice_to_have');
            }
            if (!Schema::hasColumn('job_listings', 'quality_score')) {
                $table->decimal('quality_score', 5, 2)->nullable()->after('ai_insights');
            }
            // Ensure requirements is json (may be text in some envs)
            // responsibilities may be text - cast handles JSON encoding
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            foreach (['nice_to_have', 'ai_insights', 'quality_score'] as $col) {
                if (Schema::hasColumn('job_listings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
