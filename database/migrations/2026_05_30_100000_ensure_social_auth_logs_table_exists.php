<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Re-create social_auth_logs if the original migration ran but the table
     * was never actually created (schema discrepancy detected 2026-05-30).
     * Also fixes the action/status column types: use varchar instead of
     * the original enum so new action values (redirect, disconnect, callback)
     * are accepted without a schema change.
     */
    public function up(): void
    {
        if (!Schema::hasTable('social_auth_logs')) {
            Schema::create('social_auth_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('provider', 50);
                $table->string('provider_user_id')->nullable();
                $table->string('email')->nullable();
                $table->string('action', 50);   // redirect, login, register, link, disconnect, callback, etc.
                $table->string('status', 20);   // success, failed, pending
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->text('error_message')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['provider', 'created_at']);
                $table->index(['user_id', 'created_at']);
                $table->index('action');
            });
        }
    }

    public function down(): void
    {
        // Intentionally left empty — do not drop the table on rollback,
        // since the original migration owns the lifecycle of this table.
    }
};
