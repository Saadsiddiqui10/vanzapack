<?php

namespace App\Services;

use App\Exceptions\CheckoutException;
use App\Exceptions\CouponException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartService
{
    private const COOKIE = 'gc_cart';

    private ?Cart $cart = null;

    public function __construct(
        private readonly CouponService $coupons,
        private readonly ShippingService $shipping,
        private readonly TaxService $tax,
    ) {}

    /** Resolve (or lazily create) the active cart for this request. */
    public function current(bool $create = true): ?Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        if ($user = auth()->user()) {
            $cart = Cart::firstOrNew(['user_id' => $user->id]);
        } else {
            $token = request()->cookie(self::COOKIE);
            $cart = $token ? Cart::firstOrNew(['token' => $token]) : new Cart;
        }

        if (! $cart->exists) {
            if (! $create) {
                return $this->cart = $cart->loadMissing('items');
            }

            if (! auth()->check()) {
                $cart->token = (string) Str::uuid();
                Cookie::queue(self::COOKIE, $cart->token, 60 * 24 * 30);
            }
            $cart->last_activity_at = now();
            $cart->save();
        }

        return $this->cart = $cart->load([
            'items.product.images',
            'items.product.inventory',
            'items.variant.inventory',
            'coupon',
        ]);
    }

    public function itemCount(): int
    {
        return (int) ($this->current(false)?->items->sum('quantity') ?? 0);
    }

    public function add(Product $product, ?ProductVariant $variant, int $quantity = 1): CartItem
    {
        $quantity = max(1, $quantity);
        $cart = $this->current();

        $line = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        $desiredQty = ($line?->quantity ?? 0) + $quantity;
        $this->assertStock($product, $variant, $desiredQty);

        $unitPrice = $variant ? $variant->currentPrice() : $product->currentPrice();

        if ($line) {
            $line->update(['quantity' => $desiredQty, 'unit_price' => $unitPrice]);
        } else {
            $line = $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        $this->touch($cart);

        return $line;
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($item);

            return;
        }

        $this->assertStock($item->product, $item->variant, $quantity);
        $item->update(['quantity' => $quantity]);
        $this->touch($item->cart);
    }

    public function remove(CartItem $item): void
    {
        $cart = $item->cart;
        $item->delete();
        $this->touch($cart);
    }

    public function clear(): void
    {
        $cart = $this->current(false);
        $cart?->items()->delete();
        $cart?->update(['coupon_id' => null, 'coupon_code' => null]);
    }

    // ── Coupons ─────────────────────────────────────────────────────
    public function applyCoupon(string $code): void
    {
        $cart = $this->current();
        $coupon = $this->coupons->validate($code, $cart, auth()->user());

        $cart->update(['coupon_id' => $coupon->id, 'coupon_code' => $coupon->code]);
        $cart->setRelation('coupon', $coupon);
        $this->cart = $cart;
    }

    public function removeCoupon(): void
    {
        $cart = $this->current(false);
        $cart?->update(['coupon_id' => null, 'coupon_code' => null]);
        $cart?->setRelation('coupon', null);
    }

    // ── Totals ──────────────────────────────────────────────────────
    public function totals(?int $shippingMethodId = null, string $country = 'AE'): CartTotals
    {
        $cart = $this->current(false);
        $totals = new CartTotals;

        if (! $cart || $cart->items->isEmpty()) {
            return $totals;
        }

        $totals->subtotal = round($cart->items->sum(fn (CartItem $i) => $i->lineTotal()), 2);
        $totals->itemCount = (int) $cart->items->sum('quantity');

        if ($cart->coupon) {
            try {
                $this->coupons->validate($cart->coupon->code, $cart, $cart->user);
                $totals->discount = $this->coupons->discountFor($cart->coupon, $cart);
                $totals->couponCode = $cart->coupon->code;
            } catch (CouponException) {
                $cart->update(['coupon_id' => null, 'coupon_code' => null]);
            }
        }

        if ($shippingMethodId !== null) {
            $method = $this->shipping->resolve($shippingMethodId, $totals->subtotal, $country);
            if ($method) {
                $totals->shipping = $this->shipping->costFor($method, $totals->subtotal);
                $totals->shippingMethodName = $method->name;
            }
        }

        $totals->tax = $this->tax->calculate($totals->taxableBase(), $country);

        return $totals;
    }

    // ── Guest → user merge ──────────────────────────────────────────
    public function mergeGuestCartInto(User $user): void
    {
        $token = request()->cookie(self::COOKIE);
        if (! $token) {
            return;
        }

        $guestCart = Cart::where('token', $token)->with('items')->first();
        if (! $guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                $userCart->items()->create($item->only([
                    'product_id', 'product_variant_id', 'quantity', 'unit_price',
                ]));
            }
        }

        if (! $userCart->coupon_id && $guestCart->coupon_id) {
            $userCart->update(['coupon_id' => $guestCart->coupon_id, 'coupon_code' => $guestCart->coupon_code]);
        }

        $guestCart->items()->delete();
        $guestCart->delete();
        Cookie::queue(Cookie::forget(self::COOKIE));
        $this->cart = null;
    }

    private function assertStock(Product $product, ?ProductVariant $variant, int $qty): void
    {
        if (! $product->track_inventory || $product->allow_backorder) {
            return;
        }

        $available = $variant ? $variant->availableStock() : $product->availableStock();

        if ($available < $qty) {
            throw new CheckoutException(
                "Only {$available} unit(s) of \"{$product->name}\" are available."
            );
        }
    }

    private function touch(Cart $cart): void
    {
        $cart->update(['last_activity_at' => now()]);
        $this->cart = $cart->fresh([
            'items.product.images', 'items.product.inventory',
            'items.variant.inventory', 'coupon',
        ]);
    }
}
