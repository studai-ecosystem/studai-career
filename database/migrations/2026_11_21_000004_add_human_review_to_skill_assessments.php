<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * E10: AI-generated skill assessments must be flagged for human review
     * before they are used for any consequential decision. These columns track
     * the review state.
     */
    public function up(): void
    {
        if (! Schema::hasTable('skill_assessments')) {
            return;
        }

        Schema::table('skill_assessments', function (Blueprint $table) {
            if (! Schema::hasColumn('skill_assessments', 'requires_human_review')) {
                $table->boolean('requires_human_review')->default(false)->after('status');
            }

            if (! Schema::hasColumn('skill_assessments', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('requires_human_review');
            }

            if (! Schema::hasColumn('skill_assessments', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('skill_assessments')) {
            return;
        }

        Schema::table('skill_assessments', function (Blueprint $table) {
            foreach (['requires_human_review', 'reviewed_at', 'reviewed_by'] as $column) {
                if (Schema::hasColumn('skill_assessments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
