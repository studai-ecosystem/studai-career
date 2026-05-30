<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original 2025_11_27_400000_create_social_auth_tables migration was
     * recorded as "ran" but none of its tables were actually created on production.
     * This migration re-creates social_providers and social_accounts if missing.
     * (social_auth_logs was handled by 2026_05_30_100000_ensure_social_auth_logs_table_exists)
     */
    public function up(): void
    {
        if (!Schema::hasTable('social_providers')) {
            Schema::create('social_providers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('client_id')->nullable();
                $table->text('client_secret')->nullable();
                $table->text('redirect_url')->nullable();
                $table->json('scopes')->nullable();
                $table->json('additional_config')->nullable();
                $table->string('icon')->nullable();
                $table->string('color')->nullable();
                $table->boolean('is_enabled')->default(false);
                $table->boolean('allow_login')->default(true);
                $table->boolean('allow_register')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['is_enabled', 'sort_order']);
            });
        }

        if (!Schema::hasTable('social_accounts')) {
            Schema::create('social_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('provider');
                $table->string('provider_user_id');
                $table->string('email')->nullable();
                $table->string('name')->nullable();
                $table->string('nickname')->nullable();
                $table->string('avatar')->nullable();
                $table->text('access_token')->nullable();
                $table->text('refresh_token')->nullable();
                $table->timestamp('token_expires_at')->nullable();
                $table->json('profile_data')->nullable();
                $table->timestamp('last_login_at')->nullable();
                $table->timestamps();

                $table->unique(['provider', 'provider_user_id']);
                $table->index(['user_id', 'provider']);
                $table->index('email');
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty — original migration owns lifecycle.
    }
};
