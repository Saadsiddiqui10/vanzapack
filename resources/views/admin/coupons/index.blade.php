<x-admin-layout title="Coupons" active="coupons">
    <x-admin.head title="Coupons">
        <x-slot:actions><a href="{{ route('admin.coupons.create') }}" class="btn-primary py-2 text-sm">New coupon</a></x-slot:actions>
    </x-admin.head>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Code</th><th class="px-4 py-3">Discount</th><th class="px-4 py-3">Scope</th><th class="px-4 py-3">Used</th><th class="px-4 py-3">Expires</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($coupons as $coupon)
                    <tr>
                        <td class="px-4 py-3 font-mono font-medium text-brand-800">{{ $coupon->code }}</td>
                        <td class="px-4 py-3">{{ $coupon->type->value === 'percentage' ? $coupon->value.'%' : money($coupon->value) }}</td>
                        <td class="px-4 py-3 capitalize text-slate-500">{{ $coupon->scope->value }}</td>
                        <td class="px-4 py-3">{{ $coupon->used_count }}{{ $coupon->usage_limit ? '/'.$coupon->usage_limit : '' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $coupon->expires_at?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $coupon->is_active && $coupon->isWithinSchedule() ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $coupon->is_active && $coupon->isWithinSchedule() ? 'Live' : 'Inactive' }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-brand-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Delete coupon?')">@csrf @method('DELETE')<button class="ml-2 text-rose-500 hover:underline">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $coupons->links() }}</div>
</x-admin-layout>
