<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(3, true));
        $price = fake()->randomFloat(2, 5, 250);
        $onSale = fake()->boolean(40);

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'sku' => 'VP-'.strtoupper(Str::random(8)),
            'short_description' => fake()->sentence(14),
            'description' => fake()->paragraphs(3, true),
            'specifications' => fake()->paragraph(),
            'price' => $price,
            'sale_price' => $onSale ? round($price * fake()->randomFloat(2, 0.6, 0.9), 2) : null,
            'cost_price' => round($price * 0.55, 2),
            'weight' => fake()->randomFloat(3, 0.05, 5),
            'has_variants' => false,
            'track_inventory' => true,
            'status' => ProductStatus::Active->value,
            'is_featured' => fake()->boolean(25),
            'is_new_arrival' => fake()->boolean(30),
            'is_best_seller' => fake()->boolean(20),
            'tags' => fake()->randomElements(['eco', 'bulk', 'catering', 'takeaway', 'compostable', 'premium'], 2),
            'published_at' => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            if (! $product->has_variants && $product->inventory()->doesntExist()) {
                Inventory::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'quantity' => fake()->numberBetween(0, 200),
                    'reserved' => 0,
                    'low_stock_threshold' => 10,
                ]);
            }
        });
    }

    public function outOfStock(): static
    {
        return $this->afterCreating(function (Product $product) {
            $product->inventory?->update(['quantity' => 0]);
        });
    }
}
