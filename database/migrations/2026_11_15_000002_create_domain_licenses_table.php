<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('domain_licenses')) {
            Schema::create('domain_licenses', function (Blueprint $table) {
                $table->id();
                $table->string('domain')->unique();
                $table->string('organization_name')->nullable();
                $table->foreignId('subscription_plan_id')->nullable()->constrained()->nullOnDelete();
                $table->integer('total_seats')->default(0);
                $table->integer('seats_used')->default(0);
                $table->boolean('auto_assign')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamp('expires_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['domain', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_licenses');
    }
};
