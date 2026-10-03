<x-admin-layout :title="$user->exists ? 'Edit admin user' : 'New admin user'" active="users">
    <x-admin.head :title="$user->exists ? $user->name : 'New admin user'" :back="route('admin.users.index')" />
    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}"
          class="max-w-xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($user->exists)@method('PUT')@endif
        <x-admin.field label="Name" name="name"><input name="name" value="{{ old('name', $user->name) }}" class="field"></x-admin.field>
        <x-admin.field label="Email" name="email"><input type="email" name="email" value="{{ old('email', $user->email) }}" class="field"></x-admin.field>
        <x-admin.field label="Phone" name="phone"><input name="phone" value="{{ old('phone', $user->phone) }}" class="field"></x-admin.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field :label="$user->exists ? 'New password (optional)' : 'Password'" name="password"><input type="password" name="password" class="field"></x-admin.field>
            <x-admin.field label="Confirm password" name="password_confirmation"><input type="password" name="password_confirmation" class="field"></x-admin.field>
        </div>
        <x-admin.field label="Roles" name="roles">
            <div class="space-y-1 text-sm">
                @foreach($roles as $role)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked(in_array($role->id, old('roles', $user->roles->pluck('id')->all()))) class="rounded border-slate-300 text-brand-500">
                        {{ $role->label }}
                    </label>
                @endforeach
            </div>
        </x-admin.field>
        @if($user->exists)
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="rounded border-slate-300 text-brand-500"> Account active</label>
        @endif
        <button class="btn-primary">{{ $user->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
