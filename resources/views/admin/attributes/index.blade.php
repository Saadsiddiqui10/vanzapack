<x-admin-layout title="Attributes" active="attributes">
    <x-admin.head title="Attributes">
        <x-slot:actions><a href="{{ route('admin.attributes.create') }}" class="btn-primary py-2 text-sm">New attribute</a></x-slot:actions>
    </x-admin.head>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Values</th><th class="px-4 py-3">Variation</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($attributes as $attribute)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $attribute->name }}</td>
                        <td class="px-4 py-3 capitalize text-slate-500">{{ $attribute->type }}</td>
                        <td class="px-4 py-3">{{ $attribute->values_count }}</td>
                        <td class="px-4 py-3">{{ $attribute->is_variation ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('admin.attributes.edit', $attribute) }}" class="text-brand-600 hover:underline">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
