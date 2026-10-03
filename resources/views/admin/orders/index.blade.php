<x-admin-layout title="Orders" active="orders">
    <x-admin.head title="Orders">
        <x-slot:actions><a href="{{ route('admin.exports.download', 'orders') }}" class="btn-outline py-2 text-sm">Export CSV</a></x-slot:actions>
    </x-admin.head>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Order # or email" class="field w-56 py-2 text-sm">
        <select name="status" class="field w-40 py-2 text-sm">
            <option value="">Any status</option>
            @foreach($statuses as $s)<option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>@endforeach
        </select>
        <button class="btn-navy py-2 text-sm">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Payment</th><th class="px-4 py-3">Status</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($orders as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-brand-800 hover:underline">{{ $order->number }}</a></td>
                        <td class="px-4 py-3 text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $order->user?->name ?? $order->email }}</td>
                        <td class="px-4 py-3">{{ $order->items_count }}</td>
                        <td class="px-4 py-3 font-semibold">{{ money($order->grand_total) }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $order->payment_status->badgeClasses() }}">{{ $order->payment_status->label() }}</span></td>
                        <td class="px-4 py-3"><span class="badge {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</x-admin-layout>
