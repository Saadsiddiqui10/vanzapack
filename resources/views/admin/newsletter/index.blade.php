<x-admin-layout title="Newsletter" active="newsletter">
    <x-admin.head title="Newsletter subscribers ({{ $subscribers->total() }})" />
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Email</th><th class="px-4 py-3">Subscribed</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($subscribers as $sub)
                    <tr>
                        <td class="px-4 py-3">{{ $sub->email }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $sub->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}" onsubmit="return confirm('Remove subscriber?')">@csrf @method('DELETE')<button class="text-rose-500 hover:underline">Remove</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $subscribers->links() }}</div>
</x-admin-layout>
