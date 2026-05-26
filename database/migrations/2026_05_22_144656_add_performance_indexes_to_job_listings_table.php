<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            // Composite index for the most common job search query pattern:
            // WHERE status = 'published' AND expires_at > NOW()
            $table->index(['status', 'expires_at'], 'job_listings_status_expires_at_idx');

            // Separate index for company-based filtering
            $table->index('company_id', 'job_listings_company_id_idx');

            // Index for location-based searches
            $table->index('location', 'job_listings_location_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropIndex('job_listings_status_expires_at_idx');
            $table->dropIndex('job_listings_company_id_idx');
            $table->dropIndex('job_listings_location_idx');
        });
    }
};
