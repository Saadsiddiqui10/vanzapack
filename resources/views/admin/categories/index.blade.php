<x-admin-layout title="Categories" active="categories">
    <x-admin.head title="Categories">
        <x-slot:actions><a href="{{ route('admin.categories.create') }}" class="btn-primary py-2 text-sm">New category</a></x-slot:actions>
    </x-admin.head>

    <div class="mb-4 flex gap-2 text-sm">
        <a href="{{ route('admin.categories.index') }}"
           class="rounded-lg px-3 py-1.5 {{ $trashed ? 'bg-white border border-slate-200' : 'bg-brand-700 text-white' }}">Active</a>
        <a href="{{ route('admin.categories.index', ['trashed' => 1]) }}"
           class="rounded-lg px-3 py-1.5 {{ $trashed ? 'bg-brand-700 text-white' : 'bg-white border border-slate-200' }}">
            Archived @if($archivedCount)<span class="ml-1 rounded-full bg-rose-100 px-1.5 text-xs text-rose-700">{{ $archivedCount }}</span>@endif
        </a>
    </div>

    <div x-data="{
            selected: [],
            get pageIds() { return @js($categories->pluck('id')) },
            get allChecked() { return this.pageIds.length > 0 && this.pageIds.every(id => this.selected.includes(id)) },
            toggleAll(e) { this.selected = e.target.checked ? [...new Set([...this.selected, ...this.pageIds])] : this.selected.filter(id => !this.pageIds.includes(id)) },
         }">
        <div x-show="selected.length" x-cloak
             class="mb-3 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-brand-200 bg-brand-50 px-4 py-2 text-sm">
            <span><span x-text="selected.length"></span> selected</span>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="text-slate-500 hover:underline" @click="selected = []">Clear</button>

                @if($trashed)
                    <form method="POST" action="{{ route('admin.categories.bulk-restore') }}">
                        @csrf
                        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                        <button class="btn bg-brand-700 px-3 py-1.5 text-xs text-white hover:bg-brand-800">Restore</button>
                    </form>
                    <form method="POST" action="{{ route('admin.categories.bulk-force') }}"
                          @submit="if (!confirm('PERMANENTLY delete ' + selected.length + ' category(ies)? This cannot be undone.')) $event.preventDefault()">
                        @csrf @method('DELETE')
                        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                        <button class="btn bg-rose-600 px-3 py-1.5 text-xs text-white hover:bg-rose-700">Delete permanently</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.categories.bulk-destroy') }}"
                          @submit="if (!confirm('Delete ' + selected.length + ' selected category(ies)? Categories that still contain products are skipped.')) $event.preventDefault()">
                        @csrf @method('DELETE')
                        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                        <button class="btn bg-rose-600 px-3 py-1.5 text-xs text-white hover:bg-rose-700">Delete selected</button>
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
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Parent</th>
                        <th class="px-4 py-3">Products</th>
                        <th class="px-4 py-3">{{ $trashed ? 'Archived' : 'Status' }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr :class="selected.includes({{ $category->id }}) && 'bg-brand-50/50'">
                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $category->id }}" x-model.number="selected" class="rounded border-slate-300 text-brand-500">
                            </td>
                            <td class="px-4 py-3 font-medium text-brand-800">{{ $category->name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $category->parent?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $category->products_count }}</td>
                            <td class="px-4 py-3">
                                @if($trashed)
                                    <span class="text-slate-500">{{ $category->deleted_at?->diffForHumans() }}</span>
                                @else
                                    <span class="badge {{ $category->is_active ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $category->is_active ? 'Active' : 'Hidden' }}</span>
                                    @if($category->is_featured)<span class="badge bg-sky-100 text-sky-800">Featured</span>@endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if($trashed)
                                    <form method="POST" action="{{ route('admin.categories.restore', $category->id) }}" class="inline">
                                        @csrf @method('PUT')
                                        <button class="text-brand-700 hover:underline">Restore</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.categories.force-destroy', $category->id) }}" class="inline"
                                          onsubmit="return confirm('Permanently delete this category? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="ml-2 text-rose-500 hover:underline">Delete permanently</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-brand-600 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Archive this category?')">
                                        @csrf @method('DELETE')
                                        <button class="ml-2 text-rose-500 hover:underline">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">
                            {{ $trashed ? 'No archived categories.' : 'No categories yet.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
</x-admin-layout>
