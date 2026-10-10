<x-admin-layout title="Dashboard" active="dashboard">
    <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        @foreach([
            ['Total revenue', money($totalRevenue), 'brand'],
            ["Today's revenue", money($todayRevenue), 'navy'],
            ['This month', money($monthRevenue), 'navy'],
            ['Orders', number_format($ordersCount), 'navy'],
            ['Pending orders', $pendingOrders, 'amber'],
            ['Customers', number_format($customersCount), 'navy'],
            ['Low stock', $lowStockCount, 'amber'],
            ['Out of stock', $outOfStockCount, 'rose'],
        ] as [$label, $value, $tone])
            <div class="min-w-0 rounded-xl border border-slate-200 bg-white p-3 sm:p-4">
                <p class="truncate text-[11px] uppercase tracking-wide text-slate-400 sm:text-xs">{{ $label }}</p>
                <p class="mt-1 truncate text-lg font-bold sm:text-2xl text-{{ $tone === 'brand' ? 'brand-600' : ($tone === 'amber' ? 'amber-600' : ($tone === 'rose' ? 'rose-600' : 'brand-800')) }}">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 lg:col-span-2">
            <h2 class="mb-4 font-semibold text-brand-800">Revenue — last 12 months</h2>
            @php $max = max(1, $revenueByMonth->max() ?? 1); @endphp
            <div class="flex h-48 items-end gap-2">
                @foreach($revenueByMonth as $ym => $total)
                    <div class="group flex flex-1 flex-col items-center gap-1">
                        <span class="text-[10px] text-slate-400 opacity-0 group-hover:opacity-100">{{ money($total) }}</span>
                        <div class="w-full rounded-t bg-brand-500" style="height: {{ max(3, $total / $max * 160) }}px"></div>
                        <span class="text-[10px] text-slate-400">{{ \Illuminate\Support\Carbon::parse($ym.'-01')->format('M') }}</span>
                    </div>
                @endforeach
                @if($revenueByMonth->isEmpty())<p class="text-sm text-slate-400">No revenue data yet.</p>@endif
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="mb-4 font-semibold text-brand-800">Orders by status</h2>
            <ul class="space-y-2 text-sm">
                @foreach($ordersByStatus as $status => $count)
                    <li class="flex items-center justify-between">
                        <span class="capitalize text-slate-500">{{ $status }}</span>
                        <span class="font-semibold text-brand-800">{{ $count }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-semibold text-brand-800">Recent orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-600 hover:underline">All orders →</a>
            </div>
            <table class="w-full text-sm">
                <tbody>
                @foreach($recentOrders as $order)
                    <tr class="border-b border-slate-100 last:border-0">
                        <td class="py-2"><a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-brand-800 hover:underline">{{ $order->number }}</a></td>
                        <td class="py-2 text-slate-500">{{ $order->user?->name ?? $order->email }}</td>
                        <td class="py-2"><span class="badge {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span></td>
                        <td class="py-2 text-right font-semibold">{{ money($order->grand_total) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="mb-3 font-semibold text-brand-800">Best-selling products</h2>
            <ul class="space-y-2 text-sm">
                @foreach($topProducts as $product)
                    <li class="flex items-center justify-between">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-brand-800 hover:underline">{{ \Illuminate\Support\Str::limit($product->name, 40) }}</a>
                        <span class="text-slate-500">{{ $product->sales_count }} sold</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-admin-layout>
