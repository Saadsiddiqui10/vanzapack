@php $wa = app(\App\Services\WhatsAppService::class); @endphp

<x-storefront-layout title="Order Confirmed">
    <div class="container-page py-12">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-2xl text-brand-700">✓</div>
            <h1 class="mt-4 font-display text-2xl font-bold text-brand-800">Thank you for your order!</h1>
            <p class="mt-1 text-slate-500">Order <span class="font-semibold text-brand-800">{{ $order->number }}</span> has been placed. A confirmation email is on its way.</p>
        </div>

        <div class="mx-auto mt-8 max-w-2xl card p-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 text-sm">
                <div>
                    <p class="text-slate-400">Payment</p>
                    <p class="font-medium text-brand-800">{{ $order->payment_method->label() }} — {{ $order->payment_status->label() }}</p>
                </div>
                <div class="text-right">
                    <p class="text-slate-400">Delivery</p>
                    <p class="font-medium text-brand-800">{{ $order->shipping_method_name }}</p>
                </div>
            </div>

            <ul class="divide-y divide-slate-100">
                @foreach($order->items as $item)
                    <li class="flex justify-between py-3 text-sm">
                        <span>{{ $item->quantity }} × {{ $item->name }}{{ $item->variantText() ? ' ('.$item->variantText().')' : '' }}</span>
                        <span>{{ money($item->line_total) }}</span>
                    </li>
                @endforeach
            </ul>

            <dl class="space-y-1 border-t border-slate-100 pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ money($order->subtotal) }}</dd></div>
                @if($order->discount_total > 0)<div class="flex justify-between text-brand-700"><dt>Discount</dt><dd>−{{ money($order->discount_total) }}</dd></div>@endif
                <div class="flex justify-between"><dt class="text-slate-500">Shipping</dt><dd>{{ money($order->shipping_total) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">VAT</dt><dd>{{ money($order->tax_total) }}</dd></div>
                <div class="flex justify-between text-base font-bold text-brand-800"><dt>Total</dt><dd>{{ money($order->grand_total) }}</dd></div>
            </dl>

            <div class="mt-6 flex flex-wrap gap-3">
                @auth
                    <a href="{{ route('account.orders.show', $order->number) }}" class="btn-primary">View order</a>
                @endauth
                <a href="{{ route('shop.index') }}" class="btn-outline">Continue shopping</a>
                @if($wa->enabledForOrder())
                    <button type="button" data-wa-href="{{ $wa->orderLink($order) }}" class="btn bg-[#25D366] text-white hover:opacity-90">Contact us on WhatsApp</button>
                @endif
            </div>
        </div>
    </div>
</x-storefront-layout>
