<x-admin-layout title="Reports" active="reports">
    <x-admin.head title="Sales reports" />

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-2">
        <div>
            <label class="label">Period</label>
            <select name="preset" class="field w-44 py-2 text-sm" onchange="this.form.submit()">
                @foreach(['today' => 'Today', 'yesterday' => 'Yesterday', 'last_7' => 'Last 7 days', 'last_30' => 'Last 30 days', 'this_month' => 'This month', 'last_month' => 'Last month', 'custom' => 'Custom'] as $v => $l)
                    <option value="{{ $v }}" @selected($preset === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        @if($preset === 'custom')
            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="field py-2 text-sm">
            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="field py-2 text-sm">
            <button class="btn-navy py-2 text-sm">Apply</button>
        @endif
    </form>

    <div class="grid gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs uppercase text-slate-400">Revenue</p><p class="text-2xl font-bold text-brand-600">{{ money($revenue) }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs uppercase text-slate-400">Orders</p><p class="text-2xl font-bold text-brand-800">{{ $orderCount }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs uppercase text-slate-400">Avg order</p><p class="text-2xl font-bold text-brand-800">{{ money($avgOrder) }}</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-xs uppercase text-slate-400">New customers</p><p class="text-2xl font-bold text-brand-800">{{ $newCustomers }}</p></div>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="mb-4 font-semibold text-brand-800">Daily revenue</h3>
            @php $max = max(1, $dailyRevenue->max() ?? 1); @endphp
            <div class="flex h-40 items-end gap-1">
                @foreach($dailyRevenue as $day => $total)
                    <div class="flex-1 rounded-t bg-brand-500" style="height: {{ max(2, $total / $max * 150) }}px" title="{{ $day }}: {{ money($total) }}"></div>
                @endforeach
                @if($dailyRevenue->isEmpty())<p class="text-sm text-slate-400">No sales in this period.</p>@endif
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="mb-3 font-semibold text-brand-800">Top products</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                @foreach($topProducts as $p)
                    <tr><td class="py-2">{{ \Illuminate\Support\Str::limit($p->name, 40) }}</td><td class="py-2 text-right text-slate-500">{{ $p->qty }} · {{ money($p->total) }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-5">
        <h3 class="mb-3 font-semibold text-brand-800">By status</h3>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
            @foreach($statusBreakdown as $row)
                <tr><td class="py-2 capitalize">{{ $row->status }}</td><td class="py-2">{{ $row->c }} orders</td><td class="py-2 text-right">{{ money($row->total) }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex gap-2">
        <a href="{{ route('admin.exports.download', 'orders') }}" class="btn-outline py-2 text-sm">Orders CSV</a>
        <a href="{{ route('admin.exports.download', 'products') }}" class="btn-outline py-2 text-sm">Products CSV</a>
        <a href="{{ route('admin.exports.download', 'customers') }}" class="btn-outline py-2 text-sm">Customers CSV</a>
        <a href="{{ route('admin.exports.download', 'inventory') }}" class="btn-outline py-2 text-sm">Inventory CSV</a>
    </div>
</x-admin-layout>
