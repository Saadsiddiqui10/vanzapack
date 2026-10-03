<x-admin-layout title="Customers" active="customers">
    <x-admin.head title="Customers">
        <x-slot:actions><a href="{{ route('admin.exports.download', 'customers') }}" class="btn-outline py-2 text-sm">Export CSV</a></x-slot:actions>
    </x-admin.head>

    <form method="GET" class="mb-4"><input name="q" value="{{ request('q') }}" placeholder="Search name or email" class="field w-64 py-2 text-sm"></form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Orders</th><th class="px-4 py-3">Lifetime</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($customers as $customer)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $customer->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $customer->email }}</td>
                        <td class="px-4 py-3">{{ $customer->orders_count }}</td>
                        <td class="px-4 py-3">{{ money($customer->orders_total ?? 0) }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $customer->is_active ? 'bg-brand-100 text-brand-800' : 'bg-rose-100 text-rose-700' }}">{{ $customer->is_active ? 'Active' : 'Blocked' }}</span></td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('admin.customers.show', $customer) }}" class="text-brand-600 hover:underline">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $customers->links() }}</div>
</x-admin-layout>
