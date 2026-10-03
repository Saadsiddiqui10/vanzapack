<x-admin-layout title="Tax" active="tax">
    <x-admin.head title="Tax rates">
        <x-slot:actions><a href="{{ route('admin.tax-rates.create') }}" class="btn-primary py-2 text-sm">New rate</a></x-slot:actions>
    </x-admin.head>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Country</th><th class="px-4 py-3">Class</th><th class="px-4 py-3">Rate</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($rates as $rate)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $rate->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $rate->country ?? 'Any' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $rate->tax_class }}</td>
                        <td class="px-4 py-3">{{ rtrim(rtrim(number_format($rate->rate, 3), '0'), '.') }}%</td>
                        <td class="px-4 py-3"><span class="badge {{ $rate->is_active ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $rate->is_active ? 'Active' : 'Off' }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.tax-rates.edit', $rate) }}" class="text-brand-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.tax-rates.destroy', $rate) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="ml-2 text-rose-500 hover:underline">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
