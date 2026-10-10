@php $wa = app(\App\Services\WhatsAppService::class); @endphp

<x-account-layout :title="'Order '.$order->number">
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <span class="badge {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span>
        <span class="badge {{ $order->payment_status->badgeClasses() }}">Payment: {{ $order->payment_status->label() }}</span>
        @if($order->tracking_number)
            <span class="badge bg-slate-100 text-slate-700">Tracking: {{ $order->tracking_number }}</span>
        @endif
        <span class="text-sm text-slate-400">Placed {{ $order->created_at->format('d M Y, H:i') }}</span>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <div class="card p-5">
                <h2 class="mb-3 font-semibold text-brand-800">Items</h2>
                <ul class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <li class="flex gap-3 py-3">
                            <img src="{{ $item->product?->primaryImageUrl() ?? 'https://placehold.co/80' }}" alt="" class="h-14 w-14 rounded object-cover">
                            <div class="flex-1 text-sm">
                                <p class="font-medium text-brand-800">{{ $item->name }}</p>
                                <p class="text-slate-400">{{ $item->sku }}{{ $item->variantText() ? ' · '.$item->variantText() : '' }}</p>
                                <p class="text-slate-400">Qty {{ $item->quantity }} × {{ money($item->unit_price) }}</p>
                            </div>
                            <p class="text-sm font-semibold text-brand-800">{{ money($item->line_total) }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card p-5">
                <h2 class="mb-3 font-semibold text-brand-800">Status history</h2>
                <ol class="space-y-2 text-sm">
                    @foreach($order->statusHistory as $history)
                        <li class="flex justify-between">
                            <span class="text-brand-800">{{ ucfirst($history->to_status) }}</span>
                            <span class="text-slate-400">{{ $history->created_at->format('d M Y, H:i') }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card p-5 text-sm">
                <h2 class="mb-3 font-semibold text-brand-800">Summary</h2>
                <dl class="space-y-1">
                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ money($order->subtotal) }}</dd></div>
                    @if($order->discount_total > 0)<div class="flex justify-between text-brand-700"><dt>Discount</dt><dd>−{{ money($order->discount_total) }}</dd></div>@endif
                    <div class="flex justify-between"><dt class="text-slate-500">Shipping</dt><dd>{{ money($order->shipping_total) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">VAT</dt><dd>{{ money($order->tax_total) }}</dd></div>
                    <div class="flex justify-between border-t border-slate-100 pt-1 font-bold text-brand-800"><dt>Total</dt><dd>{{ money($order->grand_total) }}</dd></div>
                </dl>
            </div>

            <div class="card p-5 text-sm">
                <h2 class="mb-2 font-semibold text-brand-800">Shipping address</h2>
                <address class="not-italic text-slate-500">
                    {{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}<br>
                    {{ $order->shipping_address['line1'] }}<br>
                    @if($order->shipping_address['line2'] ?? null){{ $order->shipping_address['line2'] }}<br>@endif
                    {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['country'] }}<br>
                    {{ $order->shipping_address['phone'] }}
                </address>
                <p class="mt-2 text-slate-400">{{ $order->shipping_method_name }}</p>
            </div>

            <div class="card space-y-2 p-5">
                @if($order->isCancellable())
                    <form method="POST" action="{{ route('account.orders.cancel', $order->number) }}"
                          onsubmit="return confirm('Cancel this order?')">
                        @csrf
                        <button class="btn-outline w-full text-rose-600">Cancel order</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('account.orders.reorder', $order->number) }}">
                    @csrf
                    <button class="btn-navy w-full">Reorder</button>
                </form>
                @if($wa->enabledForOrder())
                    <button type="button" data-wa-href="{{ $wa->orderLink($order) }}" class="btn w-full bg-[#25D366] text-white hover:opacity-90">Contact on WhatsApp</button>
                @endif
            </div>
        </div>
    </div>
</x-account-layout>
