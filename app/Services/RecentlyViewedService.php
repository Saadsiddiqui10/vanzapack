<?php

namespace App\Services;

use App\Models\Product;
use App\Models\RecentlyViewedProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RecentlyViewedService
{
    private const COOKIE = 'gc_rv';

    public function record(Product $product): void
    {
        RecentlyViewedProduct::updateOrCreate(
            $this->keys() + ['product_id' => $product->id],
            ['viewed_at' => now()],
        );

        RecentlyViewedProduct::where($this->keys())
            ->orderByDesc('viewed_at')
            ->skip(20)
            ->take(PHP_INT_MAX)
            ->get()
            ->each->delete();
    }

    /** @return Collection<int,Product> */
    public function products(int $limit = 8, ?int $excludeProductId = null): Collection
    {
        return RecentlyViewedProduct::with('product.images')
            ->where($this->keys())
            ->when($excludeProductId, fn ($q) => $q->where('product_id', '!=', $excludeProductId))
            ->orderByDesc('viewed_at')
            ->take($limit)
            ->get()
            ->pluck('product')
            ->filter(fn (?Product $p) => $p && $p->status->value === 'active')
            ->values();
    }

    private function keys(): array
    {
        if ($id = auth()->id()) {
            return ['user_id' => $id];
        }

        $token = request()->cookie(self::COOKIE);
        if (! $token) {
            $token = (string) Str::uuid();
            cookie()->queue(self::COOKIE, $token, 60 * 24 * 30);
        }

        return ['session_token' => $token];
    }
}
