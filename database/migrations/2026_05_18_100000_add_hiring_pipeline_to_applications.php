<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Pipeline stage tracking
            $table->string('hiring_stage')->nullable()->after('status');  // company_info_test | aptitude | one_on_one | hired
            $table->date('pipeline_stage_date')->nullable()->after('hiring_stage');  // scheduled date for next stage
            $table->text('pipeline_stage_notes')->nullable()->after('pipeline_stage_date');
            $table->boolean('confirmation_email_sent')->default(false)->after('pipeline_stage_notes');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['hiring_stage', 'pipeline_stage_date', 'pipeline_stage_notes', 'confirmation_email_sent']);
        });
    }
};
