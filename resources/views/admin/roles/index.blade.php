<x-admin-layout title="Roles" active="roles">
    <x-admin.head title="Roles &amp; permissions">
        <x-slot:actions><a href="{{ route('admin.roles.create') }}" class="btn-primary py-2 text-sm">New role</a></x-slot:actions>
    </x-admin.head>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Role</th><th class="px-4 py-3">Permissions</th><th class="px-4 py-3">Users</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($roles as $role)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $role->label }}</td>
                        <td class="px-4 py-3">{{ $role->name === 'super-admin' ? 'All' : $role->permissions_count }}</td>
                        <td class="px-4 py-3">{{ $role->users_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-brand-600 hover:underline">Edit</a>
                            @unless(in_array($role->name, ['super-admin', 'customer']))
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('Delete role?')">@csrf @method('DELETE')<button class="ml-2 text-rose-500 hover:underline">Delete</button></form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
