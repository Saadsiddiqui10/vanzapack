<x-admin-layout title="Shipping" active="shipping">
    <x-admin.head title="Shipping methods">
        <x-slot:actions><a href="{{ route('admin.shipping-methods.create') }}" class="btn-primary py-2 text-sm">New method</a></x-slot:actions>
    </x-admin.head>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Zone</th><th class="px-4 py-3">Price</th><th class="px-4 py-3">Free over</th><th class="px-4 py-3">ETA</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($methods as $method)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $method->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $method->zone }}{{ $method->country ? ' ('.$method->country.')' : '' }}</td>
                        <td class="px-4 py-3">{{ money($method->price) }}</td>
                        <td class="px-4 py-3">{{ $method->free_shipping_threshold ? money($method->free_shipping_threshold) : '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $method->estimated_delivery }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $method->is_active ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $method->is_active ? 'Active' : 'Off' }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.shipping-methods.edit', $method) }}" class="text-brand-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.shipping-methods.destroy', $method) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="ml-2 text-rose-500 hover:underline">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
