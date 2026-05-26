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
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'linkedin_url')) {
                $table->string('linkedin_url', 255)->nullable()->after('website');
            }
            if (!Schema::hasColumn('companies', 'culture')) {
                $table->text('culture')->nullable()->after('description');
            }
            if (!Schema::hasColumn('companies', 'logo_url')) {
                $table->string('logo_url', 255)->nullable()->after('logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumnIfExists(['linkedin_url', 'culture', 'logo_url']);
        });
    }
};
