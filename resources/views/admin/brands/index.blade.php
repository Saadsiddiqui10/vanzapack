<x-admin-layout title="Brands" active="brands">
    <x-admin.head title="Brands">
        <x-slot:actions><a href="{{ route('admin.brands.create') }}" class="btn-primary py-2 text-sm">New brand</a></x-slot:actions>
    </x-admin.head>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Products</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($brands as $brand)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $brand->name }}</td>
                        <td class="px-4 py-3">{{ $brand->products_count }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $brand->is_active ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $brand->is_active ? 'Active' : 'Hidden' }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="text-brand-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="inline" onsubmit="return confirm('Delete brand?')">@csrf @method('DELETE')<button class="ml-2 text-rose-500 hover:underline">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $brands->links() }}</div>
</x-admin-layout>
