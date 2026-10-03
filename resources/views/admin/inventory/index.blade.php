<x-admin-layout title="Inventory" active="inventory">
    <x-admin.head title="Inventory">
        <x-slot:actions><a href="{{ route('admin.exports.download', 'inventory') }}" class="btn-outline py-2 text-sm">Export CSV</a></x-slot:actions>
    </x-admin.head>

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        @foreach(['' => 'All', 'low' => 'Low stock', 'out' => 'Out of stock'] as $val => $label)
            <a href="{{ route('admin.inventory.index', array_filter(['filter' => $val])) }}"
               class="rounded-lg px-3 py-1.5 {{ request('filter', '') === $val ? 'bg-brand-700 text-white' : 'bg-white border border-slate-200' }}">{{ $label }}</a>
        @endforeach
        <form method="GET" class="ml-auto"><input name="q" value="{{ request('q') }}" placeholder="Search product" class="field w-56 py-1.5 text-sm"></form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">Variant</th><th class="px-4 py-3">On hand</th><th class="px-4 py-3">Reserved</th><th class="px-4 py-3">Available</th><th class="px-4 py-3">Adjust</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($inventories as $inv)
                    <tr class="{{ $inv->available() <= 0 ? 'bg-rose-50' : ($inv->isLow() ? 'bg-amber-50' : '') }}">
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $inv->product?->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $inv->variant?->label() ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $inv->quantity }}</td>
                        <td class="px-4 py-3">{{ $inv->reserved }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $inv->available() }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.inventory.update', $inv) }}" class="flex items-center gap-1">
                                @csrf @method('PUT')
                                <input type="number" name="quantity" value="{{ $inv->quantity }}" class="field w-20 py-1 text-sm">
                                <input type="number" name="low_stock_threshold" value="{{ $inv->low_stock_threshold }}" class="field w-16 py-1 text-sm" title="Low-stock threshold">
                                <button class="btn-navy px-2 py-1 text-xs">Save</button>
                                <a href="{{ route('admin.inventory.history', $inv) }}" class="text-xs text-slate-400 hover:underline">History</a>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $inventories->links() }}</div>
</x-admin-layout>
