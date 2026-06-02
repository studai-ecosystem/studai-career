<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * F4: Record the candidate's affirmative acknowledgment of anti-cheat
 * monitoring (tab-switch / focus-loss / time-anomaly tracking) before an
 * evaluation session may begin.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluation_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('evaluation_sessions', 'monitoring_consent_at')) {
                $table->timestamp('monitoring_consent_at')->nullable()->after('flagged_for_review');
            }
            if (! Schema::hasColumn('evaluation_sessions', 'monitoring_consent_ip')) {
                $table->string('monitoring_consent_ip', 45)->nullable()->after('monitoring_consent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('evaluation_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('evaluation_sessions', 'monitoring_consent_ip')) {
                $table->dropColumn('monitoring_consent_ip');
            }
            if (Schema::hasColumn('evaluation_sessions', 'monitoring_consent_at')) {
                $table->dropColumn('monitoring_consent_at');
            }
        });
    }
};
