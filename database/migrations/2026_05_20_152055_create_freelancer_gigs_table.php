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
        Schema::create('freelancer_gigs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_profile_id')->constrained('freelancer_profiles')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('category');
            $table->json('packages');              // [{type,title,price,delivery_days,revisions,features[]}]
            $table->json('tags')->nullable();
            $table->json('faq')->nullable();       // [{q,a}]
            $table->text('requirements')->nullable();
            $table->string('status')->default('active'); // draft|active|paused
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(5.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_featured']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancer_gigs');
    }
};
