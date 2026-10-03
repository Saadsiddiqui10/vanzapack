<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Services\OrderNumberGenerator;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $numbers = app(OrderNumberGenerator::class);
        $customers = User::whereHas('roles', fn ($q) => $q->where('name', 'customer'))->get();
        $products = Product::with('variants')->where('status', 'active')->get();
        $shipping = ShippingMethod::active()->get();

        $statuses = [
            OrderStatus::Delivered, OrderStatus::Delivered, OrderStatus::Delivered,
            OrderStatus::Shipped, OrderStatus::Processing, OrderStatus::Confirmed,
            OrderStatus::Pending, OrderStatus::Cancelled,
        ];

        foreach (range(1, 28) as $i) {
            $customer = $customers->random();
            $address = $customer->addresses->first()?->toSnapshot() ?? [
                'first_name' => 'Guest', 'last_name' => 'Buyer', 'phone' => '+971500000000',
                'line1' => 'Somewhere', 'line2' => null, 'city' => 'Dubai',
                'state' => 'Dubai', 'postal_code' => '00000', 'country' => 'AE',
            ];
            $status = fake()->randomElement($statuses);
            $method = fake()->randomElement([PaymentMethod::CashOnDelivery, PaymentMethod::BankTransfer]);
            $ship = $shipping->random();
            $createdAt = now()->subDays(fake()->numberBetween(0, 90))->subHours(fake()->numberBetween(0, 23));

            $lineProducts = $products->random(fake()->numberBetween(1, 4));
            $subtotal = 0;
            $items = [];
            foreach ($lineProducts as $product) {
                $variant = $product->has_variants ? $product->variants->random() : null;
                $qty = fake()->numberBetween(1, 6);
                $unit = $variant ? $variant->currentPrice() : $product->currentPrice();
                $lineTotal = round($unit * $qty, 2);
                $subtotal += $lineTotal;
                $items[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'name' => $product->name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'variant_label' => $variant ? [$variant->label()] : null,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'line_total' => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 2);
            $shippingTotal = $ship->costFor($subtotal);
            $taxTotal = round($subtotal * 0.05, 2);
            $grand = round($subtotal + $shippingTotal + $taxTotal, 2);

            $paid = in_array($status, [OrderStatus::Delivered, OrderStatus::Shipped, OrderStatus::Processing], true);

            $order = Order::create([
                'number' => $numbers->generate(),
                'user_id' => $customer->id,
                'email' => $customer->email,
                'phone' => $customer->phone ?? '+971500000000',
                'status' => $status->value,
                'payment_status' => $paid ? PaymentStatus::Paid->value : PaymentStatus::Pending->value,
                'payment_method' => $method->value,
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'shipping_total' => $shippingTotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grand,
                'currency' => 'AED',
                'shipping_method_id' => $ship->id,
                'shipping_method_name' => $ship->name,
                'tracking_number' => in_array($status, [OrderStatus::Shipped, OrderStatus::Delivered], true)
                    ? 'GC'.fake()->numerify('##########') : null,
                'shipping_address' => $address,
                'billing_address' => $address,
                'customer_note' => fake()->boolean(20) ? fake()->sentence() : null,
                'confirmed_at' => $status !== OrderStatus::Pending ? $createdAt->copy()->addHours(1) : null,
                'shipped_at' => in_array($status, [OrderStatus::Shipped, OrderStatus::Delivered], true) ? $createdAt->copy()->addDays(1) : null,
                'delivered_at' => $status === OrderStatus::Delivered ? $createdAt->copy()->addDays(3) : null,
                'cancelled_at' => $status === OrderStatus::Cancelled ? $createdAt->copy()->addHours(5) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($items as $item) {
                OrderItem::create($item + ['order_id' => $order->id]);
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => $method->value,
                'status' => $paid ? PaymentStatus::Paid->value : PaymentStatus::Pending->value,
                'amount' => $grand,
                'currency' => 'AED',
                'transaction_reference' => $paid ? 'TXN-'.strtoupper(fake()->bothify('????####')) : null,
                'paid_at' => $paid ? $createdAt->copy()->addHours(2) : null,
            ]);

            if ($status === OrderStatus::Delivered) {
                foreach ($order->items as $line) {
                    $product = $line->product;
                    if ($product) {
                        $product->increment('sales_count', $line->quantity);
                    }
                }
            }
        }
    }
}
