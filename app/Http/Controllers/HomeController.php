<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index()
    {
        $productCard = ['images', 'brand', 'inventory', 'inventories'];

        return view('storefront.home', [
            'heroBanners' => Banner::active()->placement('hero')->get(),
            'promoBanners' => Banner::active()->placement('promo')->get(),
            'featuredCategories' => $this->withTreeProductCounts(
                Category::active()->where('is_featured', true)->orderBy('position')->take(8)->get()
            ),
            'shopByCategory' => Category::active()->roots()->orderBy('position')->take(12)->get(),
            'newArrivals' => Product::active()->newArrivals()->with($productCard)
                ->latest('published_at')->take(10)->get(),
            'bestSellers' => Product::active()->bestSellers()->with($productCard)
                ->orderByDesc('sales_count')->take(10)->get(),
            'featuredProducts' => Product::active()->featured()->with($productCard)
                ->withCount(['approvedReviews as reviews_count'])->take(8)->get(),
            'dealProducts' => Product::active()->whereNotNull('sale_price')
                ->whereColumn('sale_price', '<', 'price')->with($productCard)
                ->orderByRaw('(price - sale_price) / price DESC')->take(10)->get(),
            'brands' => Brand::active()->featured()->take(20)->get()
                ->whenEmpty(fn () => Brand::active()->orderBy('name')->take(12)->get()),
        ]);
    }

    /**
     * Set products_count to the active products in each category *and* its sub-categories,
     * so parent categories don't show "0 products". Two queries, resolved in memory.
     */
    private function withTreeProductCounts(Collection $categories): Collection
    {
        $childrenOf = Category::active()->get(['id', 'parent_id'])->groupBy('parent_id')
            ->map(fn ($group) => $group->pluck('id')->all());
        $direct = Product::active()->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')->pluck('total', 'category_id');

        $count = function (int $id, array $seen = []) use (&$count, $childrenOf, $direct): int {
            if (isset($seen[$id])) {
                return 0; // guard against a parent loop
            }
            $seen[$id] = true;

            return (int) ($direct[$id] ?? 0)
                + array_sum(array_map(fn ($child) => $count($child, $seen), $childrenOf[$id] ?? []));
        };

        return $categories->each(fn (Category $c) => $c->products_count = $count($c->id));
    }
}
