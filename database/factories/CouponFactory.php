<?php

namespace Database\Factories;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'description' => fake()->sentence(6),
            'type' => DiscountType::Percentage->value,
            'value' => 10,
            'scope' => CouponScope::Global->value,
            'min_order_total' => null,
            'max_discount' => null,
            'usage_limit' => null,
            'per_user_limit' => null,
            'used_count' => 0,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
            'is_active' => true,
        ];
    }

    public function fixed(float $amount): static
    {
        return $this->state(fn () => [
            'type' => DiscountType::Fixed->value,
            'value' => $amount,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->subDay(),
        ]);
    }
}
