<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductQuery;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private readonly ProductQuery $productQuery) {}

    public function index(Request $request)
    {
        $term = $request->string('q')->toString();

        $products = $this->productQuery->paginate($request);

        return view('storefront.search', [
            'term' => $term,
            'products' => $products,
            'sorts' => ProductQuery::SORTS,
        ]);
    }

    public function suggest(Request $request)
    {
        $term = trim($request->string('q')->toString());

        if (mb_strlen($term) < 2) {
            return response()->json(['products' => [], 'categories' => []]);
        }

        $products = Product::active()
            ->search($term)
            ->with('images')
            ->take(6)
            ->get()
            ->map(fn (Product $p) => [
                'name' => $p->name,
                'url' => $p->url(),
                'price' => money($p->currentPrice()),
                'image' => $p->primaryImageUrl(),
            ]);

        $categories = Category::active()
            ->where('name', 'like', "%{$term}%")
            ->take(4)
            ->get()
            ->map(fn (Category $c) => [
                'name' => $c->name,
                'url' => route('category.show', $c->slug),
            ]);

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
