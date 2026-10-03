<x-admin-layout :title="'Order '.$order->number" active="orders">
    <x-admin.head :title="'Order '.$order->number" :back="route('admin.orders.index')">
        <x-slot:actions>
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn-outline py-2 text-sm">Invoice</a>
            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                  onsubmit="return confirm('Permanently delete order {{ $order->number }}? Stock is returned. This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn py-2 text-sm bg-rose-600 text-white hover:bg-rose-700">Delete order</button>
            </form>
        </x-slot:actions>
    </x-admin.head>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-3 font-semibold text-brand-800">Items</h3>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->name }}<span class="text-slate-400">{{ $item->variantText() ? ' · '.$item->variantText() : '' }}</span></td>
                                <td class="py-2 text-slate-500">{{ $item->sku }}</td>
                                <td class="py-2 text-center">{{ $item->quantity }}</td>
                                <td class="py-2 text-right">{{ money($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <dl class="mt-3 space-y-1 border-t border-slate-100 pt-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ money($order->subtotal) }}</dd></div>
                    @if($order->discount_total > 0)<div class="flex justify-between text-brand-700"><dt>Discount ({{ $order->coupon_code }})</dt><dd>−{{ money($order->discount_total) }}</dd></div>@endif
                    <div class="flex justify-between"><dt class="text-slate-500">Shipping</dt><dd>{{ money($order->shipping_total) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Tax</dt><dd>{{ money($order->tax_total) }}</dd></div>
                    <div class="flex justify-between font-bold text-brand-800"><dt>Total</dt><dd>{{ money($order->grand_total) }}</dd></div>
                </dl>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-3 font-semibold text-brand-800">Status history</h3>
                <ol class="space-y-1 text-sm">
                    @foreach($order->statusHistory as $h)
                        <li class="flex justify-between"><span class="capitalize text-brand-800">{{ $h->to_status }}</span>
                            <span class="text-slate-400">{{ $h->created_at->format('d M H:i') }} {{ $h->user ? '· '.$h->user->name : '' }}</span></li>
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-3 font-semibold text-brand-800">Update status</h3>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <select name="status" class="field text-sm">
                        @foreach($statuses as $s)<option value="{{ $s->value }}" @selected($order->status === $s)>{{ $s->label() }}</option>@endforeach
                    </select>
                    <input name="note" placeholder="Note (optional)" class="field text-sm">
                    <button class="btn-primary w-full text-sm">Update status</button>
                </form>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-3 font-semibold text-brand-800">Fulfilment</h3>
                <form method="POST" action="{{ route('admin.orders.details', $order) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <label class="label">Payment status</label>
                    <select name="payment_status" class="field text-sm">
                        @foreach(['pending', 'awaiting_confirmation', 'paid', 'failed', 'refunded', 'partially_refunded'] as $ps)
                            <option value="{{ $ps }}" @selected($order->payment_status->value === $ps)>{{ ucwords(str_replace('_', ' ', $ps)) }}</option>
                        @endforeach
                    </select>
                    <label class="label">Tracking number</label>
                    <input name="tracking_number" value="{{ $order->tracking_number }}" class="field text-sm">
                    <label class="label">Staff note</label>
                    <textarea name="staff_note" rows="3" class="field text-sm">{{ $order->staff_note }}</textarea>
                    <button class="btn-navy w-full text-sm">Save</button>
                </form>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm">
                <h3 class="mb-2 font-semibold text-brand-800">Customer</h3>
                <p>{{ $order->user?->name ?? 'Guest' }}</p>
                <p class="text-slate-500">{{ $order->email }}</p>
                <p class="text-slate-500">{{ $order->phone }}</p>
                <h3 class="mb-1 mt-3 font-semibold text-brand-800">Ship to</h3>
                <address class="not-italic text-slate-500">
                    {{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }},
                    {{ $order->shipping_address['line1'] }}, {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['country'] }}
                </address>
                <p class="mt-1 text-slate-400">{{ $order->shipping_method_name }}</p>
            </div>
        </div>
    </div>
</x-admin-layout>
