<x-admin-layout title="Admin Users" active="users">
    <x-admin.head title="Admin users">
        <x-slot:actions><a href="{{ route('admin.users.create') }}" class="btn-primary py-2 text-sm">New admin user</a></x-slot:actions>
    </x-admin.head>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Roles</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->roles->pluck('label')->implode(', ') }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $user->is_active ? 'bg-brand-100 text-brand-800' : 'bg-rose-100 text-rose-700' }}">{{ $user->is_active ? 'Active' : 'Disabled' }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-brand-600 hover:underline">Edit</a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Remove admin user?')">@csrf @method('DELETE')<button class="ml-2 text-rose-500 hover:underline">Delete</button></form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</x-admin-layout>
