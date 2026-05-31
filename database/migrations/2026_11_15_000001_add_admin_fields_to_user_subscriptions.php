<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('user_subscriptions', 'bonus_ai_credits')) {
                $table->integer('bonus_ai_credits')->default(0)->after('ai_credits_used_this_month');
            }
            if (! Schema::hasColumn('user_subscriptions', 'is_admin_managed')) {
                $table->boolean('is_admin_managed')->default(false)->after('bonus_ai_credits');
            }
            if (! Schema::hasColumn('user_subscriptions', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('is_admin_managed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            foreach (['bonus_ai_credits', 'is_admin_managed', 'admin_notes'] as $column) {
                if (Schema::hasColumn('user_subscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
