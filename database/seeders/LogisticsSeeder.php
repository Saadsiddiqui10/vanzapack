<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class LogisticsSeeder extends Seeder
{
    public function run(): void
    {
        ShippingMethod::insert([
            [
                'name' => 'Standard Delivery (UAE)',
                'description' => 'Delivered in 2-4 business days across the UAE.',
                'zone' => 'Domestic', 'country' => 'AE', 'city' => null,
                'price' => 15.00, 'free_shipping_threshold' => 99.00,
                'estimated_delivery' => '2-4 business days',
                'is_active' => true, 'position' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name' => 'Next-Day Delivery (Dubai)',
                'description' => 'Order before 2PM for next-day delivery within Dubai.',
                'zone' => 'Dubai', 'country' => 'AE', 'city' => 'Dubai',
                'price' => 25.00, 'free_shipping_threshold' => null,
                'estimated_delivery' => 'Next business day',
                'is_active' => true, 'position' => 2,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name' => 'GCC Freight',
                'description' => 'Cross-border delivery to GCC countries.',
                'zone' => 'GCC', 'country' => null, 'city' => null,
                'price' => 60.00, 'free_shipping_threshold' => 500.00,
                'estimated_delivery' => '5-8 business days',
                'is_active' => true, 'position' => 3,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        TaxRate::insert([
            [
                'name' => 'UAE VAT', 'country' => 'AE', 'state' => null,
                'tax_class' => 'standard', 'rate' => 5.000,
                'is_active' => true, 'priority' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name' => 'Zero-rated', 'country' => 'AE', 'state' => null,
                'tax_class' => 'zero', 'rate' => 0.000,
                'is_active' => true, 'priority' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
