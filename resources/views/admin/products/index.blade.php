<x-admin-layout title="Products" active="products">
    <x-admin.head title="Products">
        <x-slot:actions>
            <a href="{{ route('admin.exports.download', 'products') }}" class="btn-outline py-2 text-sm">Export CSV</a>
            <a href="{{ route('admin.products.import.form') }}" class="btn-outline py-2 text-sm">Import CSV / Photos</a>
            <a href="{{ route('admin.products.create') }}" class="btn-primary py-2 text-sm">New product</a>
        </x-slot:actions>
    </x-admin.head>

    {{-- Products / Bin tabs ("Bin" = deleted products that can still be restored) --}}
    <div class="mb-4 flex gap-2 text-sm">
        <a href="{{ route('admin.products.index') }}"
           class="rounded-lg px-3 py-1.5 {{ $trashed ? 'bg-white border border-slate-200' : 'bg-brand-700 text-white' }}">Products</a>
        <a href="{{ route('admin.products.index', ['trashed' => 1]) }}"
           class="rounded-lg px-3 py-1.5 {{ $trashed ? 'bg-brand-700 text-white' : 'bg-white border border-slate-200' }}">
            Bin @if($archivedCount)<span class="ml-1 rounded-full bg-rose-100 px-1.5 text-xs text-rose-700">{{ $archivedCount }}</span>@endif
        </a>
    </div>

    {{-- All drafts at once (every page, not only the ticked rows) --}}
    @if(! $trashed && $draftCount > 0)
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm">
            <p class="text-amber-900">
                <strong>{{ $draftCount }} draft {{ Str::plural('product', $draftCount) }}</strong> — not visible in the store.
                <a href="{{ route('admin.products.index', ['status' => 'draft']) }}" class="ml-1 underline hover:no-underline">View drafts</a>
            </p>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.products.drafts-status') }}"
                      onsubmit="return confirm('Make all {{ $draftCount }} draft products active (visible in the store)?')">
                    @csrf <input type="hidden" name="status" value="active">
                    <button class="btn bg-brand-700 px-3 py-1.5 text-xs text-white hover:bg-brand-800">Make all active</button>
                </form>
                <form method="POST" action="{{ route('admin.products.drafts-status') }}"
                      onsubmit="return confirm('Archive all {{ $draftCount }} draft products?')">
                    @csrf <input type="hidden" name="status" value="archived">
                    <button class="btn border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50">Archive all</button>
                </form>
            </div>
        </div>
    @endif

    {{-- Stock for all / out-of-stock / low-stock products at once (every page) --}}
    @unless($trashed)
        <details class="mb-4 rounded-xl border border-slate-200 bg-white text-sm" @if($outOfStockCount) open @endif>
            <summary class="flex cursor-pointer flex-wrap items-center justify-between gap-2 px-4 py-3 font-semibold text-brand-800">
                <span>Update stock in bulk</span>
                <span class="text-xs font-normal text-slate-500">
                    <span class="{{ $outOfStockCount ? 'font-semibold text-rose-600' : '' }}">{{ $outOfStockCount }} out of stock</span>
                    · {{ $lowStockCount }} low stock
                    <span class="hidden sm:inline">(counts include each size / pack)</span>
                </span>
            </summary>
            <form method="POST" action="{{ route('admin.products.bulk-stock') }}"
                  x-data="{ scope: '{{ $outOfStockCount ? 'out_of_stock' : 'all' }}', mode: 'set', qty: 100 }"
                  @submit="if (!confirm('Update stock for ' + {all: 'ALL products', out_of_stock: 'every out-of-stock item', low_stock: 'every low-stock item'}[scope] + ': ' + {set: 'set to', add: 'add', subtract: 'subtract'}[mode] + ' ' + qty + '?')) $event.preventDefault()"
                  class="flex flex-wrap items-end gap-3 border-t border-slate-100 px-4 py-3">
                @csrf
                <label class="flex flex-col gap-1">
                    <span class="text-xs text-slate-500">Apply to</span>
                    <select name="scope" x-model="scope" class="field w-48 py-1.5 text-sm">
                        <option value="all">All products</option>
                        <option value="out_of_stock">Only out of stock ({{ $outOfStockCount }})</option>
                        <option value="low_stock">Only low stock ({{ $lowStockCount }})</option>
                    </select>
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-xs text-slate-500">Action</span>
                    <select name="mode" x-model="mode" class="field w-36 py-1.5 text-sm">
                        <option value="set">Set stock to</option>
                        <option value="add">Add</option>
                        <option value="subtract">Subtract</option>
                    </select>
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-xs text-slate-500">Quantity</span>
                    <input type="number" name="quantity" x-model.number="qty" min="0" max="1000000" required class="field w-28 py-1.5 text-sm">
                </label>
                <button class="btn bg-brand-700 px-4 py-2 text-xs text-white hover:bg-brand-800">Update stock</button>
                <p class="w-full text-xs text-slate-400">Products with sizes or packs update every size. Changes are recorded in each product's stock history.</p>
            </form>
        </details>
    @endunless

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
                    {{-- Change status of the ticked products --}}
                    @foreach([
                        'active' => ['Set active', 'bg-brand-700 text-white hover:bg-brand-800'],
                        'draft' => ['Set draft', 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50'],
                        'archived' => ['Archive (hide)', 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50'],
                    ] as $status => [$label, $classes])
                        <form method="POST" action="{{ route('admin.products.bulk-status') }}">
                            @csrf <input type="hidden" name="status" value="{{ $status }}">
                            <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                            <button class="btn px-3 py-1.5 text-xs {{ $classes }}">{{ $label }}</button>
                        </form>
                    @endforeach

                    {{-- Stock for the ticked products --}}
                    <form method="POST" action="{{ route('admin.products.bulk-stock') }}" class="flex items-center gap-1"
                          x-data="{ mode: 'set' }">
                        @csrf <input type="hidden" name="scope" value="selected">
                        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                        <select name="mode" x-model="mode" class="field w-auto py-1 pl-2 pr-7 text-xs" aria-label="Stock action">
                            <option value="set">Stock =</option>
                            <option value="add">Stock +</option>
                            <option value="subtract">Stock −</option>
                        </select>
                        <input type="number" name="quantity" min="0" max="1000000" required placeholder="Qty"
                               class="field w-20 py-1 text-xs" aria-label="Quantity">
                        <button class="btn border border-brand-700 bg-white px-3 py-1.5 text-xs text-brand-800 hover:bg-brand-50">Update stock</button>
                    </form>

                    <form method="POST" action="{{ route('admin.products.bulk-destroy') }}"
                          @submit="if (!confirm('Move ' + selected.length + ' selected product(s) to the bin?')) $event.preventDefault()">
                        @csrf @method('DELETE')
                        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                        <button class="btn bg-rose-600 px-3 py-1.5 text-xs text-white hover:bg-rose-700">Move to bin</button>
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
                        <th class="px-4 py-3">{{ $trashed ? 'Moved to bin' : 'Stock' }}</th>
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
