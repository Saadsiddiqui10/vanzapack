<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->number }}</title>
    <style>
        body { font-family: system-ui, sans-serif; color: #1e293b; padding: 40px; max-width: 720px; margin: auto; }
        h1 { color: #000066; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .right { text-align: right; }
        .totals td { border: none; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Print</button>
    <h1>VanzaPack</h1>
    <p>{{ settings('store_address', config('store.address')) }}<br>{{ settings('store_email', config('store.email')) }}</p>
    <hr>
    <h2>Invoice {{ $order->number }}</h2>
    <p>Date: {{ $order->created_at->format('d M Y') }}<br>
       Bill to: {{ $order->billing_address['first_name'] }} {{ $order->billing_address['last_name'] }},
       {{ $order->billing_address['line1'] }}, {{ $order->billing_address['city'] }}, {{ $order->billing_address['country'] }}</p>

    <table>
        <thead><tr><th>Item</th><th>SKU</th><th class="right">Qty</th><th class="right">Unit</th><th class="right">Total</th></tr></thead>
        <tbody>
        @foreach($order->items as $item)
            <tr><td>{{ $item->name }}</td><td>{{ $item->sku }}</td><td class="right">{{ $item->quantity }}</td><td class="right">{{ money($item->unit_price) }}</td><td class="right">{{ money($item->line_total) }}</td></tr>
        @endforeach
        </tbody>
        <tfoot class="totals">
            <tr><td colspan="4" class="right">Subtotal</td><td class="right">{{ money($order->subtotal) }}</td></tr>
            @if($order->discount_total > 0)<tr><td colspan="4" class="right">Discount</td><td class="right">-{{ money($order->discount_total) }}</td></tr>@endif
            <tr><td colspan="4" class="right">Shipping</td><td class="right">{{ money($order->shipping_total) }}</td></tr>
            <tr><td colspan="4" class="right">VAT</td><td class="right">{{ money($order->tax_total) }}</td></tr>
            <tr><td colspan="4" class="right"><strong>Total</strong></td><td class="right"><strong>{{ money($order->grand_total) }}</strong></td></tr>
        </tfoot>
    </table>

    <p style="margin-top:24px">Payment method: {{ $order->payment_method->label() }} — {{ $order->payment_status->label() }}</p>
</body>
</html>
