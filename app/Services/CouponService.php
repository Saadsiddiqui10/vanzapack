<?php

namespace App\Services;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use App\Exceptions\CouponException;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\User;

class CouponService
{
    /**
     * @throws CouponException
     */
    public function validate(string $code, Cart $cart, ?User $user = null): Coupon
    {
        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])->first();

        if (! $coupon || ! $coupon->is_active) {
            throw new CouponException('This coupon code is not valid.');
        }

        if (! $coupon->isWithinSchedule()) {
            throw new CouponException('This coupon has expired or is not active yet.');
        }

        if (! $coupon->hasUsesLeft()) {
            throw new CouponException('This coupon has reached its usage limit.');
        }

        if ($user && $coupon->per_user_limit !== null) {
            $usedByUser = $coupon->usages()->where('user_id', $user->id)->count();
            if ($usedByUser >= $coupon->per_user_limit) {
                throw new CouponException('You have already used this coupon.');
            }
        }

        $eligibleSubtotal = $this->eligibleSubtotal($coupon, $cart);

        if ($eligibleSubtotal <= 0) {
            throw new CouponException('This coupon does not apply to the items in your cart.');
        }

        if ($coupon->min_order_total !== null && $this->cartSubtotal($cart) < (float) $coupon->min_order_total) {
            throw new CouponException('Add '.money($coupon->min_order_total).' worth of items to use this coupon.');
        }

        return $coupon;
    }

    public function discountFor(Coupon $coupon, Cart $cart): float
    {
        $base = $this->eligibleSubtotal($coupon, $cart);

        $discount = $coupon->type === DiscountType::Percentage
            ? $base * ((float) $coupon->value / 100)
            : (float) $coupon->value;

        if ($coupon->max_discount !== null) {
            $discount = min($discount, (float) $coupon->max_discount);
        }

        return round(min($discount, $this->cartSubtotal($cart)), 2);
    }

    private function cartSubtotal(Cart $cart): float
    {
        return round($cart->items->sum(fn ($i) => (float) $i->unit_price * $i->quantity), 2);
    }

    private function eligibleSubtotal(Coupon $coupon, Cart $cart): float
    {
        if ($coupon->scope === CouponScope::Global) {
            return $this->cartSubtotal($cart);
        }

        return round($cart->items->filter(function ($item) use ($coupon) {
            $product = $item->product;
            if (! $product) {
                return false;
            }

            return $coupon->scope === CouponScope::Product
                ? in_array($product->id, $coupon->product_ids ?? [], true)
                : in_array($product->category_id, $coupon->category_ids ?? [], true);
        })->sum(fn ($i) => (float) $i->unit_price * $i->quantity), 2);
    }
}
