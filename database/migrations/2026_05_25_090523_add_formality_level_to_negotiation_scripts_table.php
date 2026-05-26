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
        Schema::table('negotiation_scripts', function (Blueprint $table) {
            $table->string('formality_level')->nullable()->after('tone');
            $table->boolean('includes_data')->default(false)->after('includes_alternatives');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('negotiation_scripts', function (Blueprint $table) {
            $table->dropColumn(['formality_level', 'includes_data']);
        });
    }
};
