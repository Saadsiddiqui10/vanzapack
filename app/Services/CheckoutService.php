<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Exceptions\CheckoutException;
use App\Models\Cart;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderPlacedNotification;
use App\Services\Payments\PaymentManager;
use App\Services\Payments\PaymentResult;
use App\Support\Notify;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly ShippingService $shipping,
        private readonly TaxService $tax,
        private readonly CouponService $coupons,
        private readonly InventoryService $inventory,
        private readonly OrderNumberGenerator $numbers,
        private readonly PaymentManager $payments,
    ) {}

    /**
     * @param  array{email:string,phone:string,payment_method:string,shipping_method_id:int,
     *               shipping_address:array,billing_address:array,customer_note:?string}  $data
     *
     * @throws CheckoutException
     */
    public function place(Cart $cart, array $data, ?User $user = null): array
    {
        $cart->loadMissing(['items.product', 'items.variant', 'coupon']);

        if ($cart->items->isEmpty()) {
            throw new CheckoutException('Your cart is empty.');
        }

        $country = $data['shipping_address']['country'] ?? 'AE';

        // Re-validate stock at the last moment.
        foreach ($cart->items as $item) {
            if (! $this->inventory->canFulfill($item->product, $item->variant, $item->quantity)) {
                throw new CheckoutException("\"{$item->product->name}\" is no longer available in the requested quantity.");
            }
        }

        $subtotal = round($cart->items->sum(fn ($i) => (float) $i->unit_price * $i->quantity), 2);

        $discount = 0.0;
        if ($cart->coupon) {
            $this->coupons->validate($cart->coupon->code, $cart, $user);
            $discount = $this->coupons->discountFor($cart->coupon, $cart);
        }

        $shippingMethod = $this->shipping->resolve((int) $data['shipping_method_id'], $subtotal, $country);
        if (! $shippingMethod) {
            throw new CheckoutException('Please choose a delivery method.');
        }
        $shippingTotal = $this->shipping->costFor($shippingMethod, $subtotal);

        $taxableBase = max(0, round($subtotal - $discount, 2));
        $taxTotal = $this->tax->calculate($taxableBase, $country);

        $grandTotal = round($subtotal - $discount + $shippingTotal + $taxTotal, 2);

        $method = PaymentMethod::from($data['payment_method']);

        /** @var array{order:Order,payment:PaymentResult} $outcome */
        $outcome = DB::transaction(function () use (
            $cart, $data, $user, $subtotal, $discount, $shippingTotal,
            $taxTotal, $grandTotal, $shippingMethod, $method
        ) {
            $order = Order::create([
                'number' => $this->numbers->generate(),
                'user_id' => $user?->id,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'status' => OrderStatus::Pending->value,
                'payment_status' => 'pending',
                'payment_method' => $method->value,
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'shipping_total' => $shippingTotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'currency' => settings('currency', 'AED'),
                'coupon_code' => $cart->coupon?->code,
                'shipping_method_id' => $shippingMethod->id,
                'shipping_method_name' => $shippingMethod->name,
                'shipping_address' => $data['shipping_address'],
                'billing_address' => $data['billing_address'] ?: $data['shipping_address'],
                'customer_note' => $data['customer_note'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'name' => $item->product->name,
                    'sku' => $item->variant?->sku ?? $item->product->sku,
                    'variant_label' => $item->variant
                        ? $item->variant->attributeValues->pluck('value')->all()
                        : null,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => round((float) $item->unit_price * $item->quantity, 2),
                ]);

                $this->inventory->deduct(
                    $item->product,
                    $item->variant,
                    $item->quantity,
                    $order,
                    "Order {$order->number}",
                );

                $item->product->increment('sales_count', $item->quantity);
            }

            if ($cart->coupon) {
                $cart->coupon->increment('used_count');
                CouponUsage::create([
                    'coupon_id' => $cart->coupon->id,
                    'user_id' => $user?->id,
                    'order_id' => $order->id,
                    'discount' => $discount,
                ]);
            }

            $order->statusHistory()->create([
                'from_status' => null,
                'to_status' => OrderStatus::Pending->value,
                'user_id' => $user?->id,
                'note' => 'Order placed',
            ]);

            $paymentResult = $this->payments->charge($order->fresh());

            // Clear the cart contents.
            $cart->items()->delete();
            $cart->update(['coupon_id' => null, 'coupon_code' => null]);

            return ['order' => $order->fresh(['items', 'payment']), 'payment' => $paymentResult];
        });

        /** @var Order $order */
        $order = $outcome['order'];

        // Notifications must never break a successfully placed order.
        try {
            $order->user?->notify(new OrderPlacedNotification($order));
            Notify::staff(new NewOrderNotification($order));
        } catch (\Throwable $e) {
            report($e);
        }

        return $outcome;
    }
}
