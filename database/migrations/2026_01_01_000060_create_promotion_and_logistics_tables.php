<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('type'); // fixed | percentage
            $table->decimal('value', 12, 2);
            $table->string('scope')->default('global'); // global | product | category
            $table->decimal('min_order_total', 12, 2)->nullable();
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_user_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('product_ids')->nullable();
            $table->json('category_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable();
            $table->decimal('discount', 12, 2);
            $table->timestamps();
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('zone')->default('Domestic');
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('free_shipping_threshold', 12, 2)->nullable();
            $table->string('estimated_delivery')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country', 2)->nullable();
            $table->string('state')->nullable();
            $table->string('tax_class')->default('standard');
            $table->decimal('rate', 6, 3); // percentage
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('priority')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
    }
};
