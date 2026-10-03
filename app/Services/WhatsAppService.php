<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;

class WhatsAppService
{
    public function enabledForProduct(): bool
    {
        return (bool) settings('whatsapp_product_enabled', true);
    }

    public function enabledForCart(): bool
    {
        return (bool) settings('whatsapp_cart_enabled', true);
    }

    public function enabledForOrder(): bool
    {
        return (bool) settings('whatsapp_order_enabled', true);
    }

    public function productLink(Product $product, ?ProductVariant $variant = null, int $qty = 1): string
    {
        $price = $variant ? $variant->currentPrice() : $product->currentPrice();

        $lines = [
            'Hello '.settings('store_name', 'VanzaPack').', I am interested in:',
            '',
            "Product: {$product->name}",
            'SKU: '.($variant?->sku ?? $product->sku),
        ];

        if ($variant) {
            foreach ($variant->attributeValues as $value) {
                $lines[] = "{$value->attribute->name}: {$value->value}";
            }
        }

        $lines[] = "Quantity: {$qty}";
        $lines[] = 'Price: '.money($price);
        $lines[] = '';
        $lines[] = 'Product URL: '.$product->url();

        return whatsapp_link(implode("\n", $lines));
    }

    public function cartLink(Cart $cart): string
    {
        $lines = ['Hello '.settings('store_name', 'VanzaPack').', I would like to order:', ''];

        foreach ($cart->items as $item) {
            $label = $item->variant ? ' ('.$item->variant->label().')' : '';
            $lines[] = "• {$item->quantity} × {$item->product->name}{$label} — ".money($item->lineTotal());
        }

        $lines[] = '';
        $lines[] = 'Subtotal: '.money($cart->items->sum(fn ($i) => $i->lineTotal()));

        return whatsapp_link(implode("\n", $lines));
    }

    public function orderLink(Order $order): string
    {
        $message = 'Hello '.settings('store_name', 'VanzaPack').", I have a question about my order {$order->number} "
            .'(total '.money($order->grand_total).').';

        return whatsapp_link($message);
    }

    public function supportLink(): string
    {
        return whatsapp_link(settings('whatsapp_default_message', 'Hello, I have a question.'));
    }
}
