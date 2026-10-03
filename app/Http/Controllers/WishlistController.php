<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        $wishlist = Wishlist::firstOrCreate(['user_id' => $request->user()->id]);
        $wishlist->load(['items.product.images', 'items.product.brand', 'items.product.inventory', 'items.product.inventories']);

        return view('storefront.wishlist', compact('wishlist'));
    }

    public function toggle(Request $request, Product $product)
    {
        if (! $request->user()) {
            return $this->guest($request);
        }

        $wishlist = Wishlist::firstOrCreate(['user_id' => $request->user()->id]);
        $item = $wishlist->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->delete();
            $inList = false;
            $message = 'Removed from your wishlist.';
        } else {
            $wishlist->items()->create(['product_id' => $product->id]);
            $inList = true;
            $message = 'Saved to your wishlist.';
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'in_list' => $inList, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, Product $product)
    {
        $this->requireLogin($request);
        $request->user()->wishlist?->items()->where('product_id', $product->id)->delete();

        return back()->with('success', 'Removed from your wishlist.');
    }

    private function requireLogin(Request $request): void
    {
        abort_unless($request->user(), 401);
    }

    private function guest(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'requires_login' => true,
                'message' => 'Please sign in to use your wishlist.',
                'login_url' => route('login'),
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
