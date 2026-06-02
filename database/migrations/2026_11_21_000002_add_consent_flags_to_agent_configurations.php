<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A5: Per-category autonomous-agent consent.
 *
 * The autonomous job-application agent performs several distinct autonomous
 * actions on the user's behalf (discovering jobs, customizing resumes/cover
 * letters, submitting applications, sending follow-ups). EU AI Act / informed
 * consent principles require the user to consent to each category explicitly
 * rather than via a single blanket toggle. These columns capture that consent;
 * the agent jobs enforce them before acting.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('agent_configurations')) {
            return;
        }

        Schema::table('agent_configurations', function (Blueprint $table): void {
            if (! Schema::hasColumn('agent_configurations', 'consent_discover')) {
                $table->boolean('consent_discover')->default(false)->after('enable_learning');
            }
            if (! Schema::hasColumn('agent_configurations', 'consent_customize')) {
                $table->boolean('consent_customize')->default(false)->after('consent_discover');
            }
            if (! Schema::hasColumn('agent_configurations', 'consent_submit')) {
                $table->boolean('consent_submit')->default(false)->after('consent_customize');
            }
            if (! Schema::hasColumn('agent_configurations', 'consent_follow_up')) {
                $table->boolean('consent_follow_up')->default(false)->after('consent_submit');
            }
            if (! Schema::hasColumn('agent_configurations', 'consent_recorded_at')) {
                $table->timestamp('consent_recorded_at')->nullable()->after('consent_follow_up');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('agent_configurations')) {
            return;
        }

        Schema::table('agent_configurations', function (Blueprint $table): void {
            foreach (['consent_discover', 'consent_customize', 'consent_submit', 'consent_follow_up', 'consent_recorded_at'] as $column) {
                if (Schema::hasColumn('agent_configurations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
