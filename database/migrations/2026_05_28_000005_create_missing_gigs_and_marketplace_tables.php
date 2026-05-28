<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // freelancer_gigs — was recorded in sync_historical_migrations but never created
        if (! Schema::hasTable('freelancer_gigs')) {
            Schema::create('freelancer_gigs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('freelancer_profile_id');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description');
                $table->string('category');
                $table->json('packages');
                $table->json('tags')->nullable();
                $table->json('faq')->nullable();
                $table->text('requirements')->nullable();
                $table->string('status')->default('active');
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('views_count')->default(0);
                $table->unsignedInteger('orders_count')->default(0);
                $table->decimal('average_rating', 3, 2)->default(5.00);
                $table->unsignedInteger('total_reviews')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['status', 'is_featured']);
                $table->index('category');
                $table->index('freelancer_profile_id');
            });
        }

        // gig_reviews — if missing
        if (! Schema::hasTable('gig_reviews')) {
            Schema::create('gig_reviews', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('gig_id');
                $table->unsignedBigInteger('reviewer_id');
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('review')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamps();

                $table->index('gig_id');
                $table->index('reviewer_id');
            });
        }

        // gig_orders — if missing
        if (! Schema::hasTable('gig_orders')) {
            Schema::create('gig_orders', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('gig_id');
                $table->unsignedBigInteger('buyer_id');
                $table->unsignedBigInteger('seller_id');
                $table->string('package_type')->default('basic');
                $table->decimal('amount', 12, 2);
                $table->string('currency', 10)->default('INR');
                $table->string('status')->default('pending');
                $table->integer('delivery_days')->nullable();
                $table->timestamp('deadline_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('requirements_filled')->nullable();
                $table->json('deliverables')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['gig_id', 'status']);
                $table->index('buyer_id');
                $table->index('seller_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gig_orders');
        Schema::dropIfExists('gig_reviews');
        Schema::dropIfExists('freelancer_gigs');
    }
};
