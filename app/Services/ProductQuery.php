<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductQuery
{
    public const SORTS = [
        'latest' => 'Latest',
        'popular' => 'Most Popular',
        'best_selling' => 'Best Selling',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'rating' => 'Highest Rated',
        'discount' => 'Biggest Discount',
    ];

    public function apply(Request $request, ?Builder $base = null): Builder
    {
        $query = ($base ?? Product::query())
            ->active()
            ->with(['images', 'brand', 'inventory', 'inventories'])
            ->withCount(['approvedReviews as reviews_count']);

        // Search term
        $query->search($request->string('q')->toString() ?: null);

        // Category (slug)
        if ($slug = $request->string('category')->toString()) {
            $category = Category::where('slug', $slug)->first();
            if ($category) {
                $query->whereIn('category_id', $category->descendantIds());
            }
        }

        // Brands (ids or slugs)
        if ($brands = array_filter((array) $request->input('brands', []))) {
            $query->whereHas('brand', fn ($b) => $b->whereIn('slug', $brands)->orWhereIn('id', $brands));
        }

        // Attribute values (ids)
        if ($values = array_filter((array) $request->input('attributes', []))) {
            $query->whereHas('attributeValues', fn ($v) => $v->whereIn('attribute_values.id', $values));
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        // Rating
        if ($request->filled('rating')) {
            $query->where('rating_avg', '>=', (float) $request->input('rating'));
        }

        // Availability
        if ($request->input('availability') === 'in_stock') {
            $query->where(function ($q) {
                $q->where('track_inventory', false)
                    ->orWhere('allow_backorder', true)
                    ->orWhereHas('inventories', fn ($i) => $i->whereColumn('quantity', '>', 'reserved'));
            });
        }

        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
        }

        return $this->sort($query, $request->string('sort')->toString());
    }

    public function paginate(Request $request, ?Builder $base = null, int $perPage = 12): LengthAwarePaginator
    {
        return $this->apply($request, $base)
            ->paginate($perPage)
            ->withQueryString();
    }

    private function sort(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'popular' => $query->orderByDesc('views'),
            'best_selling' => $query->orderByDesc('sales_count'),
            'price_asc' => $query->orderBy(DB::raw('COALESCE(sale_price, price)')),
            'price_desc' => $query->orderByDesc(DB::raw('COALESCE(sale_price, price)')),
            'rating' => $query->orderByDesc('rating_avg'),
            'discount' => $query->orderByRaw('CASE WHEN sale_price IS NULL THEN 0 ELSE (price - sale_price) / price END DESC'),
            default => $query->latest('published_at')->latest('id'),
        };
    }
}
