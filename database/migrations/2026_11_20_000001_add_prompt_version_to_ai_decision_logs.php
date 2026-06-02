<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * F10: Track the prompt version that produced each AI decision so audit
     * records are reproducible and can be tied back to a specific prompt
     * revision in the prompt registry.
     */
    public function up(): void
    {
        Schema::table('ai_decision_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('ai_decision_logs', 'prompt_version')) {
                $table->string('prompt_version', 100)->nullable()->after('model_used');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ai_decision_logs', function (Blueprint $table): void {
            if (Schema::hasColumn('ai_decision_logs', 'prompt_version')) {
                $table->dropColumn('prompt_version');
            }
        });
    }
};
