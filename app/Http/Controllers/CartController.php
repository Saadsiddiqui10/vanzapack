<?php

namespace App\Http\Controllers;

use App\Exceptions\CheckoutException;
use App\Exceptions\CouponException;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function index()
    {
        $cart = $this->cart->current(false);
        $totals = $this->cart->totals();

        return view('storefront.cart', compact('cart', 'totals'));
    }

    public function mini()
    {
        $cart = $this->cart->current(false);
        $totals = $this->cart->totals();

        return view('storefront.partials.cart-drawer', compact('cart', 'totals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        $product = Product::active()->findOrFail($data['product_id']);
        $variant = null;

        if ($product->has_variants) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->findOr($data['variant_id'] ?? 0, fn () => null);

            if (! $variant) {
                return $this->respond($request, false, 'Please choose the product options first.', 422);
            }
        }

        try {
            $this->cart->add($product, $variant, (int) ($data['quantity'] ?? 1));
        } catch (CheckoutException $e) {
            return $this->respond($request, false, $e->getMessage(), 422);
        }

        return $this->respond($request, true, "\"{$product->name}\" added to your cart.");
    }

    public function update(Request $request, CartItem $item)
    {
        $this->authorizeItem($item);
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:999']]);

        try {
            $this->cart->updateQuantity($item, (int) $data['quantity']);
        } catch (CheckoutException $e) {
            return $this->respond($request, false, $e->getMessage(), 422);
        }

        return $this->respond($request, true, 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $item)
    {
        $this->authorizeItem($item);
        $this->cart->remove($item);

        return $this->respond($request, true, 'Item removed from your cart.');
    }

    public function clear(Request $request)
    {
        $this->cart->clear();

        return $this->respond($request, true, 'Your cart has been cleared.');
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:60']]);

        try {
            $this->cart->applyCoupon($data['code']);
        } catch (CouponException $e) {
            return $this->respond($request, false, $e->getMessage(), 422);
        }

        return $this->respond($request, true, 'Coupon applied.');
    }

    public function removeCoupon(Request $request)
    {
        $this->cart->removeCoupon();

        return $this->respond($request, true, 'Coupon removed.');
    }

    private function authorizeItem(CartItem $item): void
    {
        $cart = $this->cart->current(false);
        abort_unless($cart && $item->cart_id === $cart->id, 403);
    }

    private function respond(Request $request, bool $ok, string $message, int $status = 200)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => $ok,
                'message' => $message,
                'cart_count' => $this->cart->itemCount(),
                'totals' => $this->cart->totals()->toArray(),
            ], $ok ? 200 : $status);
        }

        return back()->with($ok ? 'success' : 'error', $message);
    }
}
