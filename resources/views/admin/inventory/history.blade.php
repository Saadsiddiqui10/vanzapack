<x-admin-layout title="Stock history" active="inventory">
    <x-admin.head :title="$inventory->product?->name.' — stock history'" :back="route('admin.inventory.index')" />
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Change</th><th class="px-4 py-3">Balance</th><th class="px-4 py-3">By</th><th class="px-4 py-3">Note</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($inventory->movements as $m)
                    <tr>
                        <td class="px-4 py-3 text-slate-500">{{ $m->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $m->type->label() }}</td>
                        <td class="px-4 py-3 {{ $m->quantity < 0 ? 'text-rose-600' : 'text-brand-700' }}">{{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}</td>
                        <td class="px-4 py-3">{{ $m->balance_after }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $m->user?->name ?? 'System' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $m->note }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No movements recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
