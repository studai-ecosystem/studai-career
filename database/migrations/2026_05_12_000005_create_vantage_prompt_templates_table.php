<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vantage_prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // e.g. 'executive_interview', 'evaluator_v1'
            $table->string('type', 30); // executive | evaluator | scenario
            $table->string('version', 10)->default('1.0');
            $table->text('system_prompt');
            $table->json('variables')->nullable(); // list of {placeholder} variables used
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vantage_prompt_templates');
    }
};
