<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(private readonly ProductQuery $productQuery) {}

    public function index(Request $request)
    {
        return $this->render($request, [
            'title' => 'Shop All Products',
            'description' => 'Browse the full VanzaPack catalogue of packaging, tableware, hygiene and grocery supplies.',
        ]);
    }

    public function offers(Request $request)
    {
        $request->merge(['on_sale' => true]);

        return $this->render($request, [
            'title' => 'Special Offers',
            'description' => 'Current deals and discounts across the VanzaPack range.',
        ]);
    }

    public function newArrivals(Request $request)
    {
        return $this->render($request, [
            'title' => 'New Arrivals',
            'description' => 'The latest additions to the VanzaPack catalogue.',
            'base' => Product::query()->newArrivals(),
        ]);
    }

    public function bestSellers(Request $request)
    {
        return $this->render($request, [
            'title' => 'Best Sellers',
            'description' => 'Our most-ordered packaging and food-service supplies.',
            'base' => Product::query()->bestSellers(),
        ]);
    }

    public function category(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);

        return $this->render($request, [
            'title' => $category->name,
            'description' => $category->meta_description ?: $category->description,
            'category' => $category,
            'base' => Product::query()->whereIn('category_id', $category->descendantIds()),
        ]);
    }

    public function brands()
    {
        $brands = Brand::active()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->get();

        return view('storefront.brands', compact('brands'));
    }

    public function brand(Request $request, Brand $brand)
    {
        abort_unless($brand->is_active, 404);

        return $this->render($request, [
            'title' => $brand->name,
            'description' => $brand->meta_description ?: $brand->description,
            'brand' => $brand,
            'base' => Product::query()->where('brand_id', $brand->id),
        ]);
    }

    private function render(Request $request, array $context)
    {
        $base = $context['base'] ?? null;
        $products = $this->productQuery->paginate($request, $base instanceof Builder ? $base : null);

        return view('storefront.shop', [
            'products' => $products,
            'pageTitle' => $context['title'],
            'pageDescription' => $context['description'] ?? null,
            'activeCategory' => $context['category'] ?? null,
            'activeBrand' => $context['brand'] ?? null,
            'filterCategories' => Category::active()->roots()
                ->with(['children' => fn ($q) => $q->active()->withCount(['products' => fn ($p) => $p->active()])])
                ->orderBy('position')->get(),
            'filterAttributes' => Attribute::where('is_variation', true)->with('values')->orderBy('position')->get(),
            'sorts' => ProductQuery::SORTS,
            'priceBounds' => [
                'min' => (int) floor((float) Product::active()->min('price')),
                'max' => (int) ceil((float) Product::active()->max('price')),
            ],
        ]);
    }
}
