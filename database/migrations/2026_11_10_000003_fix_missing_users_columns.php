<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds missing columns to users table that were expected by additive
 * migrations (fake-recorded in sync) but never physically applied.
 * Each block is guarded — safe to run multiple times.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('remember_token');
            }
            if (! Schema::hasColumn('users', 'profile_completed_at')) {
                $table->timestamp('profile_completed_at')->nullable()->after('last_login_at');
            }
            if (! Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('avatar');
            }
            if (! Schema::hasColumn('users', 'linkedin_url')) {
                $table->string('linkedin_url')->nullable();
            }
            if (! Schema::hasColumn('users', 'github_url')) {
                $table->string('github_url')->nullable();
            }
            if (! Schema::hasColumn('users', 'portfolio_url')) {
                $table->string('portfolio_url')->nullable();
            }
            if (! Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable();
            }
            if (! Schema::hasColumn('users', 'experience_years')) {
                $table->unsignedTinyInteger('experience_years')->nullable();
            }
            if (! Schema::hasColumn('users', 'current_role')) {
                $table->string('current_role')->nullable();
            }
            if (! Schema::hasColumn('users', 'current_company')) {
                $table->string('current_company')->nullable();
            }
            if (! Schema::hasColumn('users', 'ai_credits')) {
                $table->integer('ai_credits')->default(0);
            }
            if (! Schema::hasColumn('users', 'stripe_id')) {
                $table->string('stripe_id')->nullable()->index();
            }
            if (! Schema::hasColumn('users', 'pm_type')) {
                $table->string('pm_type')->nullable();
            }
            if (! Schema::hasColumn('users', 'pm_last_four')) {
                $table->string('pm_last_four', 4)->nullable();
            }
            if (! Schema::hasColumn('users', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }
            if (! Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable();
            }
            if (! Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable();
            }
            if (! Schema::hasColumn('users', 'provider_token')) {
                $table->string('provider_token')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Left intentionally bare — no destructive rollbacks in production
    }
};
