<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'description' => '10% off your first order',
            'type' => 'percentage', 'value' => 10, 'scope' => 'global',
            'min_order_total' => null, 'max_discount' => 50,
            'usage_limit' => null, 'per_user_limit' => 1,
            'starts_at' => now()->subMonth(), 'expires_at' => now()->addYear(),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'BULK50',
            'description' => 'AED 50 off orders over AED 400',
            'type' => 'fixed', 'value' => 50, 'scope' => 'global',
            'min_order_total' => 400, 'max_discount' => null,
            'usage_limit' => 500, 'per_user_limit' => 3,
            'starts_at' => now()->subWeek(), 'expires_at' => now()->addMonths(3),
            'is_active' => true,
        ]);

        $tissues = Category::where('name', 'Facial Tissues')->first();
        Coupon::create([
            'code' => 'TISSUE15',
            'description' => '15% off facial tissue',
            'type' => 'percentage', 'value' => 15, 'scope' => 'category',
            'category_ids' => $tissues ? [$tissues->id] : [],
            'min_order_total' => null, 'max_discount' => null,
            'usage_limit' => null, 'per_user_limit' => null,
            'starts_at' => now()->subDays(3), 'expires_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'EXPIRED20',
            'description' => 'Expired test coupon',
            'type' => 'percentage', 'value' => 20, 'scope' => 'global',
            'starts_at' => now()->subMonths(2), 'expires_at' => now()->subMonth(),
            'is_active' => true,
        ]);
    }
}
