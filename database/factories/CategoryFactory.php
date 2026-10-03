<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.Str::random(4),
            'description' => fake()->sentence(12),
            'is_active' => true,
            'is_featured' => false,
            'position' => fake()->numberBetween(0, 20),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
