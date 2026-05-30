<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The canonical payment_transactions table (2025_10_28_162824) only stores
     * subscription_plan_id, but the PaymentTransaction model, SubscriptionController
     * and AdminAnalyticsController all reference user_subscription_id. The drift
     * migration that added it was skipped because the table already existed, so this
     * idempotent migration backfills the column safely on both SQLite and MySQL.
     */
    public function up(): void
    {
        if (! Schema::hasTable('payment_transactions')) {
            return;
        }

        if (! Schema::hasColumn('payment_transactions', 'user_subscription_id')) {
            Schema::table('payment_transactions', function (Blueprint $table): void {
                $table->unsignedBigInteger('user_subscription_id')->nullable()->after('subscription_plan_id');
                $table->index('user_subscription_id');
            });
        }

        // The PaymentTransaction model uses SoftDeletes, but the deleted_at column was
        // never created on prod (the add-soft-deletes migration was faked/skipped),
        // causing 500s on any query against the table.
        if (! Schema::hasColumn('payment_transactions', 'deleted_at')) {
            Schema::table('payment_transactions', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        // No-op: keep the column to avoid dropping data on rollback.
    }
};
