<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 500);
        $shipping = 15;
        $tax = round($subtotal * 0.05, 2);

        $address = [
            'first_name' => fake()->firstName(), 'last_name' => fake()->lastName(),
            'phone' => '+971500000000', 'line1' => fake()->streetAddress(), 'line2' => null,
            'city' => 'Dubai', 'state' => 'Dubai', 'postal_code' => '00000', 'country' => 'AE',
        ];

        return [
            'number' => 'ORD-'.now()->year.'-'.fake()->unique()->numerify('######'),
            'email' => fake()->safeEmail(),
            'phone' => '+971500000000',
            'status' => OrderStatus::Pending->value,
            'payment_status' => PaymentStatus::Pending->value,
            'payment_method' => PaymentMethod::CashOnDelivery->value,
            'subtotal' => $subtotal,
            'discount_total' => 0,
            'shipping_total' => $shipping,
            'tax_total' => $tax,
            'grand_total' => round($subtotal + $shipping + $tax, 2),
            'currency' => 'AED',
            'shipping_address' => $address,
            'billing_address' => $address,
        ];
    }
}
