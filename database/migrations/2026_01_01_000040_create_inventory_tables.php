<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->integer('reserved')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->timestamps();
            $table->unique(['product_id', 'product_variant_id']);
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->integer('quantity'); // signed delta
            $table->integer('balance_after');
            $table->nullableMorphs('reference'); // e.g. order, adjustment
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventories');
    }
};
