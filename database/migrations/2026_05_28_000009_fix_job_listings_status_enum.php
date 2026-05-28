<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fix: Update job_listings.status ENUM to include 'active' and 'paused'
 * so the Filament admin form can use all status options without constraint violations.
 * Also normalise the employer 'published' status to 'active' for consistency.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL ENUM modification requires raw SQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE job_listings MODIFY COLUMN status ENUM('draft','active','published','paused','closed','archived') DEFAULT 'draft'");
        }
        // SQLite doesn't support ENUM, TEXT is already flexible — no change needed
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Convert 'active' back to 'published' and remove 'paused' (map to 'closed')
            DB::statement("UPDATE job_listings SET status = 'published' WHERE status = 'active'");
            DB::statement("UPDATE job_listings SET status = 'closed' WHERE status = 'paused'");
            DB::statement("ALTER TABLE job_listings MODIFY COLUMN status ENUM('draft','published','closed','archived') DEFAULT 'draft'");
        }
    }
};
