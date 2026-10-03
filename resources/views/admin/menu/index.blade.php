<x-admin-layout title="Navigation Menu" active="menu">
    <x-admin.head title="Navigation menu">
        <x-slot:actions><a href="{{ route('admin.menu-items.create') }}" class="btn-primary py-2 text-sm">New menu item</a></x-slot:actions>
    </x-admin.head>

    <p class="mb-4 text-sm text-slate-500">
        Controls the main storefront navigation (Home, Shop by Category, …). Set “Position” to reorder (lower = left).
        A <strong>Category dropdown</strong> item shows the full mega-menu of categories.
    </p>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Position</th><th class="px-4 py-3">Label</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Link</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $item)
                    <tr>
                        <td class="px-4 py-3 text-slate-500">{{ $item->position }}</td>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $item->label }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $item->type === 'mega' ? 'Category dropdown' : 'Link' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $item->url ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $item->is_active ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $item->is_active ? 'Visible' : 'Hidden' }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.menu-items.edit', $item) }}" class="text-brand-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.menu-items.destroy', $item) }}" class="inline" onsubmit="return confirm('Remove this menu item?')">
                                @csrf @method('DELETE')
                                <button class="ml-2 text-rose-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No menu items — the storefront falls back to Home / Shop.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
