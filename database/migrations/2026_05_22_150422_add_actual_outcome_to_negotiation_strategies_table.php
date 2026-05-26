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
        Schema::table('negotiation_strategies', function (Blueprint $table) {
            if (!Schema::hasColumn('negotiation_strategies', 'actual_outcome')) {
                $table->decimal('actual_outcome', 10, 2)->nullable()->after('status');
            }
            if (!Schema::hasColumn('negotiation_strategies', 'actual_outcome_date')) {
                $table->timestamp('actual_outcome_date')->nullable()->after('actual_outcome');
            }
        });
    }

    public function down(): void
    {
        Schema::table('negotiation_strategies', function (Blueprint $table) {
            if (Schema::hasColumn('negotiation_strategies', 'actual_outcome')) {
                $table->dropColumn('actual_outcome');
            }
            if (Schema::hasColumn('negotiation_strategies', 'actual_outcome_date')) {
                $table->dropColumn('actual_outcome_date');
            }
        });
    }
};
