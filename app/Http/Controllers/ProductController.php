<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Services\RecentlyViewedService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Request $request, Product $product, RecentlyViewedService $recentlyViewed)
    {
        abort_unless($product->status->value === 'active', 404);

        $product->load([
            'images', 'brand', 'category.parent',
            'variants.attributeValues.attribute', 'variants.inventory',
            'inventory', 'inventories',
            'attributeValues.attribute',
            'approvedReviews.user', 'approvedReviews.images',
        ]);

        $product->incrementQuietly('views');
        $recentlyViewed->record($product);

        $canReview = false;
        if ($user = $request->user()) {
            $canReview = OrderItem::where('product_id', $product->id)
                ->where('is_reviewed', false)
                ->whereHas('order', fn ($q) => $q->where('user_id', $user->id)
                    ->whereIn('status', ['delivered', 'shipped']))
                ->exists();
        }

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['images', 'brand', 'inventory', 'inventories'])
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('storefront.product', [
            'product' => $product,
            'variantOptions' => $this->variantMatrix($product),
            'related' => $related,
            'recentlyViewed' => $recentlyViewed->products(8, $product->id),
            'canReview' => $canReview,
            'ratingBreakdown' => $this->ratingBreakdown($product),
        ]);
    }

    public function quickView(Product $product)
    {
        abort_unless($product->status->value === 'active', 404);
        $product->load(['images', 'brand', 'variants.attributeValues.attribute', 'variants.inventory', 'inventory', 'inventories']);

        return view('storefront.partials.quick-view', [
            'product' => $product,
            'variantOptions' => $this->variantMatrix($product),
        ]);
    }

    /** Resolve a variant + its live price/stock from selected attribute-value ids. */
    public function variant(Request $request, Product $product)
    {
        $valueIds = collect($request->input('values', []))->map(fn ($v) => (int) $v)->filter()->sort()->values();

        $variant = $product->variants()
            ->with('inventory')
            ->get()
            ->first(function ($v) use ($valueIds) {
                $ids = $v->attributeValues->pluck('id')->sort()->values();

                return $ids->count() === $valueIds->count() && $ids->all() === $valueIds->all();
            });

        if (! $variant) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'variant_id' => $variant->id,
            'sku' => $variant->sku,
            'price' => $variant->currentPrice(),
            'price_formatted' => money($variant->currentPrice()),
            'in_stock' => $variant->inStock(),
            'stock' => $variant->availableStock(),
            'image' => $variant->imageUrl(),
        ]);
    }

    private function variantMatrix(Product $product): array
    {
        if (! $product->has_variants) {
            return [];
        }

        $attributes = [];
        foreach ($product->variants as $variant) {
            foreach ($variant->attributeValues as $value) {
                $attributes[$value->attribute->name]['attribute'] = $value->attribute;
                $attributes[$value->attribute->name]['values'][$value->id] = $value;
            }
        }

        return $attributes;
    }

    private function ratingBreakdown(Product $product): array
    {
        $counts = $product->reviews()
            ->where('status', 'approved')
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        return collect(range(5, 1))
            ->mapWithKeys(fn ($star) => [$star => (int) ($counts[$star] ?? 0)])
            ->all();
    }
}
