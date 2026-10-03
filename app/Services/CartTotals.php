<?php

namespace App\Services;

class CartTotals
{
    public function __construct(
        public float $subtotal = 0,
        public float $discount = 0,
        public float $shipping = 0,
        public float $tax = 0,
        public int $itemCount = 0,
        public ?string $couponCode = null,
        public ?string $shippingMethodName = null,
    ) {}

    public function grandTotal(): float
    {
        return round($this->subtotal - $this->discount + $this->shipping + $this->tax, 2);
    }

    public function taxableBase(): float
    {
        return max(0, round($this->subtotal - $this->discount, 2));
    }

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'shipping' => $this->shipping,
            'tax' => $this->tax,
            'grand_total' => $this->grandTotal(),
            'item_count' => $this->itemCount,
            'coupon_code' => $this->couponCode,
        ];
    }
}
