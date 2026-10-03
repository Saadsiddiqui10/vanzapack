<x-admin-layout title="Announcement Bar" active="announcements">
    <x-admin.head title="Announcement bar">
        <x-slot:actions><a href="{{ route('admin.announcements.create') }}" class="btn-primary py-2 text-sm">New announcement</a></x-slot:actions>
    </x-admin.head>

    <p class="mb-4 text-sm text-slate-500">These messages scroll across the green bar at the very top of the storefront. Add as many as you like; drag order via the “Position” number (lower = first).</p>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Position</th><th class="px-4 py-3">Text</th><th class="px-4 py-3">Link</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($announcements as $a)
                    <tr>
                        <td class="px-4 py-3 text-slate-500">{{ $a->position }}</td>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $a->text }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $a->url ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $a->is_active ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $a->is_active ? 'Active' : 'Off' }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.announcements.edit', $a) }}" class="text-brand-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}" class="inline" onsubmit="return confirm('Remove this announcement?')">
                                @csrf @method('DELETE')
                                <button class="ml-2 text-rose-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No announcements. The top bar is hidden.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
