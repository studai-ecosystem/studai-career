<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->string('company_email')->nullable()->after('website');
            $table->string('hr_email')->nullable()->after('company_email');
            $table->string('contact_phone')->nullable()->after('hr_email');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn(['company_email', 'hr_email', 'contact_phone']);
        });
    }
};
