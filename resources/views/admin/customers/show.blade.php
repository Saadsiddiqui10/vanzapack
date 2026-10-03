<x-admin-layout :title="$user->name" active="customers">
    <x-admin.head :title="$user->name" :back="route('admin.customers.index')" />

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm">
                <p class="font-semibold text-brand-800">{{ $user->email }}</p>
                <p class="text-slate-500">{{ $user->phone }}</p>
                <p class="text-slate-400">Joined {{ $user->created_at->format('d M Y') }}</p>
                <form method="POST" action="{{ route('admin.customers.update', $user) }}" class="mt-3 space-y-2">
                    @csrf @method('PUT')
                    <label class="label">Customer group</label>
                    <input name="customer_group" value="{{ $user->customer_group }}" class="field text-sm">
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="rounded border-slate-300 text-brand-500"> Account active</label>
                    <button class="btn-navy w-full text-sm">Save</button>
                </form>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm">
                <h3 class="mb-2 font-semibold text-brand-800">Addresses</h3>
                @forelse($user->addresses as $a)
                    <p class="text-slate-500">{{ $a->fullName() }} — {{ $a->line1 }}, {{ $a->city }}</p>
                @empty <p class="text-slate-400">None</p> @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 lg:col-span-2">
            <h3 class="mb-3 font-semibold text-brand-800">Orders</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                @forelse($user->orders as $order)
                    <tr>
                        <td class="py-2"><a href="{{ route('admin.orders.show', $order) }}" class="text-brand-800 hover:underline">{{ $order->number }}</a></td>
                        <td class="py-2 text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="py-2"><span class="badge {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span></td>
                        <td class="py-2 text-right font-semibold">{{ money($order->grand_total) }}</td>
                    </tr>
                @empty <tr><td class="py-4 text-slate-400">No orders</td></tr> @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
