<?php

namespace Database\Seeders;

use App\Enums\ReviewStatus;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::whereHas('roles', fn ($q) => $q->where('name', 'customer'))->get();
        $titles = ['Great quality', 'Exactly as described', 'Good value for bulk', 'Will reorder', 'Sturdy and reliable', 'Fast delivery'];

        Product::inRandomOrder()->take(30)->get()->each(function (Product $product) use ($customers, $titles) {
            $reviewers = $customers->random(min(4, $customers->count()));
            foreach ($reviewers as $customer) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $customer->id,
                    'rating' => fake()->numberBetween(3, 5),
                    'title' => fake()->randomElement($titles),
                    'comment' => fake()->paragraph(3),
                    'status' => fake()->boolean(85)
                        ? ReviewStatus::Approved->value
                        : ReviewStatus::Pending->value,
                    'is_verified_purchase' => fake()->boolean(70),
                ]);
            }
        });
    }
}
