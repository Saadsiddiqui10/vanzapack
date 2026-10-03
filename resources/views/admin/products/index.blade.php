<x-admin-layout title="Products" active="products">
    <x-admin.head title="Products">
        <x-slot:actions>
            <a href="{{ route('admin.exports.download', 'products') }}" class="btn-outline py-2 text-sm">Export CSV</a>
            <a href="{{ route('admin.products.import.form') }}" class="btn-outline py-2 text-sm">Import CSV</a>
            <a href="{{ route('admin.products.create') }}" class="btn-primary py-2 text-sm">New product</a>
        </x-slot:actions>
    </x-admin.head>

    {{-- Active / Archived tabs --}}
    <div class="mb-4 flex gap-2 text-sm">
        <a href="{{ route('admin.products.index') }}"
           class="rounded-lg px-3 py-1.5 {{ $trashed ? 'bg-white border border-slate-200' : 'bg-brand-700 text-white' }}">Active</a>
        <a href="{{ route('admin.products.index', ['trashed' => 1]) }}"
           class="rounded-lg px-3 py-1.5 {{ $trashed ? 'bg-brand-700 text-white' : 'bg-white border border-slate-200' }}">
            Archived @if($archivedCount)<span class="ml-1 rounded-full bg-rose-100 px-1.5 text-xs text-rose-700">{{ $archivedCount }}</span>@endif
        </a>
    </div>

    @unless($trashed)
        <form method="GET" class="mb-4 flex flex-wrap gap-2">
            <input name="q" value="{{ request('q') }}" placeholder="Search name or SKU" class="field w-64 py-2 text-sm">
            <select name="category" class="field w-48 py-2 text-sm">
                <option value="">All categories</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach
            </select>
            <select name="status" class="field w-36 py-2 text-sm">
                <option value="">Any status</option>
                @foreach(['active', 'draft', 'archived'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <button class="btn-navy py-2 text-sm">Filter</button>
        </form>
    @endunless

    <div x-data="{
            selected: [],
            get pageIds() { return @js($products->pluck('id')) },
            get allChecked() { return this.pageIds.length > 0 && this.pageIds.every(id => this.selected.includes(id)) },
            toggleAll(e) { this.selected = e.target.checked ? [...new Set([...this.selected, ...this.pageIds])] : this.selected.filter(id => !this.pageIds.includes(id)) },
         }">
        {{-- Bulk action bar --}}
        <div x-show="selected.length" x-cloak
             class="mb-3 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-brand-200 bg-brand-50 px-4 py-2 text-sm">
            <span><span x-text="selected.length"></span> selected</span>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="text-slate-500 hover:underline" @click="selected = []">Clear</button>

                @if($trashed)
                    <form method="POST" action="{{ route('admin.products.bulk-restore') }}">
                        @csrf
                        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                        <button class="btn bg-brand-700 px-3 py-1.5 text-xs text-white hover:bg-brand-800">Restore</button>
                    </form>
                    <form method="POST" action="{{ route('admin.products.bulk-force') }}"
                          @submit="if (!confirm('PERMANENTLY delete ' + selected.length + ' product(s)? This cannot be undone.')) $event.preventDefault()">
                        @csrf @method('DELETE')
                        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                        <button class="btn bg-rose-600 px-3 py-1.5 text-xs text-white hover:bg-rose-700">Delete permanently</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.products.bulk-destroy') }}"
                          @submit="if (!confirm('Archive ' + selected.length + ' selected product(s)?')) $event.preventDefault()">
                        @csrf @method('DELETE')
                        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                        <button class="btn bg-rose-600 px-3 py-1.5 text-xs text-white hover:bg-rose-700">Archive selected</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox" :checked="allChecked" @change="toggleAll" class="rounded border-slate-300 text-brand-500">
                        </th>
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Price</th>
                        <th class="px-4 py-3">{{ $trashed ? 'Archived' : 'Stock' }}</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr :class="selected.includes({{ $product->id }}) && 'bg-brand-50/50'">
                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $product->id }}" x-model.number="selected" class="rounded border-slate-300 text-brand-500">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $product->primaryImageUrl() }}" alt="" class="h-9 w-9 rounded object-cover">
                                    <span class="font-medium text-brand-800">{{ \Illuminate\Support\Str::limit($product->name, 44) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $product->sku }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $product->category?->name }}</td>
                            <td class="px-4 py-3">{{ money($product->currentPrice()) }}</td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $trashed ? $product->deleted_at?->diffForHumans() : ($product->has_variants ? 'Variants' : ($product->inventory?->quantity ?? 0)) }}
                            </td>
                            <td class="px-4 py-3"><span class="badge {{ $product->status->value === 'active' ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $product->status->label() }}</span></td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if($trashed)
                                    <form method="POST" action="{{ route('admin.products.restore', $product->id) }}" class="inline">
                                        @csrf @method('PUT')
                                        <button class="text-brand-700 hover:underline">Restore</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.products.force-destroy', $product->id) }}" class="inline"
                                          onsubmit="return confirm('Permanently delete “{{ addslashes($product->name) }}”? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="ml-2 text-rose-500 hover:underline">Delete permanently</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-brand-600 hover:underline">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            {{ $trashed ? 'No archived products.' : 'No products found.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</x-admin-layout>
