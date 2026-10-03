<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (used for the test suite) cannot add foreign keys via ALTER TABLE.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('coupon_id')->references('id')->on('coupons')->nullOnDelete();
        });

        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
        });
    }
};
