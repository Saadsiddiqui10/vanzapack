<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $productCard = ['images', 'brand', 'inventory', 'inventories'];

        return view('storefront.home', [
            'heroBanners' => Banner::active()->placement('hero')->get(),
            'promoBanners' => Banner::active()->placement('promo')->get(),
            'featuredCategories' => Category::active()->where('is_featured', true)
                ->withCount(['products' => fn ($q) => $q->active()])
                ->orderBy('position')->take(8)->get(),
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
}
