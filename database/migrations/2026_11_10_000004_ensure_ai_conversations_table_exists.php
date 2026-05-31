<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * REPAIR MIGRATION — 2026-11-10
 *
 * The ai_conversations table was recorded in sync_historical_migrations as already run
 * (2025_10_28_162828_create_ai_conversations_table), but the table was never actually
 * created on production.  The original migration used an ENUM for the `context` column,
 * but AIService::trackAzureUsage / trackAnthropicUsage insert the calling PHP class name
 * (e.g. "App\Services\AI\CareerCoachService"), which is not a valid ENUM value.
 *
 * This repair migration:
 *   1. Creates the table if it does not exist.
 *   2. Uses TEXT for `context` (not ENUM) so class names can be stored freely.
 *
 * Safe to run on any environment — guarded with Schema::hasTable().
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ai_conversations')) {
            // Table exists — ensure context column is TEXT (not ENUM)
            // to support class-name values written by trackAzureUsage / trackAnthropicUsage.
            if (Schema::hasColumn('ai_conversations', 'context')) {
                // Check current column type; alter only if it is an ENUM (MySQL only).
                if (DB::getDriverName() === 'mysql') {
                    $pdo = Schema::getConnection()->getPdo();
                    $stmt = $pdo->query(
                        "SELECT DATA_TYPE FROM information_schema.COLUMNS
                         WHERE TABLE_SCHEMA = DATABASE()
                           AND TABLE_NAME   = 'ai_conversations'
                           AND COLUMN_NAME  = 'context'
                         LIMIT 1"
                    );
                    $type = $stmt ? strtolower((string) $stmt->fetchColumn()) : 'text';

                    if ($type === 'enum') {
                        Schema::table('ai_conversations', function (Blueprint $table): void {
                            $table->text('context')->change();
                        });
                    }
                }
            }
            return;
        }

        Schema::create('ai_conversations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // TEXT instead of ENUM — AIService passes the calling class name as context.
            $table->text('context');
            $table->json('messages');
            $table->integer('tokens_used')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->string('session_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
