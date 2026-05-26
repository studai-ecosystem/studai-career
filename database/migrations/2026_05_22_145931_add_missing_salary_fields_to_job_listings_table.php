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
            if (!Schema::hasColumn('job_listings', 'salary_currency')) {
                $table->string('salary_currency', 10)->default('USD')->after('salary_max');
            }
            if (!Schema::hasColumn('job_listings', 'salary_period')) {
                $table->string('salary_period', 20)->default('year')->after('salary_currency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumnIfExists('salary_currency');
            $table->dropColumnIfExists('salary_period');
        });
    }
};
