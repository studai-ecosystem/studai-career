<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * D12: Adds link-health tracking columns to learning_resources so the
     * weekly link checker can record whether each resource URL is still live.
     */
    public function up(): void
    {
        if (! Schema::hasTable('learning_resources')) {
            return;
        }

        Schema::table('learning_resources', function (Blueprint $table) {
            if (! Schema::hasColumn('learning_resources', 'link_status')) {
                $table->string('link_status')->nullable()->after('url');
            }

            if (! Schema::hasColumn('learning_resources', 'link_http_status')) {
                $table->unsignedSmallInteger('link_http_status')->nullable()->after('link_status');
            }

            if (! Schema::hasColumn('learning_resources', 'link_checked_at')) {
                $table->timestamp('link_checked_at')->nullable()->after('link_http_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('learning_resources')) {
            return;
        }

        Schema::table('learning_resources', function (Blueprint $table) {
            foreach (['link_status', 'link_http_status', 'link_checked_at'] as $column) {
                if (Schema::hasColumn('learning_resources', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
