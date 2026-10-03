<x-admin-layout title="Activity Log" active="activity">
    <x-admin.head title="Activity log" />
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">When</th><th class="px-4 py-3">User</th><th class="px-4 py-3">Action</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">IP</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($logs as $log)
                    <tr>
                        <td class="px-4 py-3 text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-4 py-3"><span class="badge bg-slate-100 text-slate-600">{{ $log->action }}</span></td>
                        <td class="px-4 py-3">{{ $log->description }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $log->ip_address }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</x-admin-layout>
