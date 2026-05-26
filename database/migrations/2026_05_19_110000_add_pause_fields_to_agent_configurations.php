<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_configurations', function (Blueprint $table) {
            $table->boolean('is_paused')->default(false)->after('is_active');
            $table->timestamp('activated_at')->nullable()->after('last_run_at');
            $table->timestamp('deactivated_at')->nullable()->after('activated_at');
        });
    }

    public function down(): void
    {
        Schema::table('agent_configurations', function (Blueprint $table) {
            $table->dropColumn(['is_paused', 'activated_at', 'deactivated_at']);
        });
    }
};
