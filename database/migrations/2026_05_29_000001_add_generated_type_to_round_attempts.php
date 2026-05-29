<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('round_attempts')) {
            return;
        }

        if (! Schema::hasColumn('round_attempts', 'generated_type')) {
            Schema::table('round_attempts', function (Blueprint $table) {
                $table->string('generated_type')->nullable()->after('questions');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('round_attempts') && Schema::hasColumn('round_attempts', 'generated_type')) {
            Schema::table('round_attempts', function (Blueprint $table) {
                $table->dropColumn('generated_type');
            });
        }
    }
};
