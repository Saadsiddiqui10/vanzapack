<x-admin-layout :title="$role->exists ? 'Edit role' : 'New role'" active="roles">
    <x-admin.head :title="$role->exists ? $role->label : 'New role'" :back="route('admin.roles.index')" />
    <form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}"
          class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($role->exists)@method('PUT')@endif
        <x-admin.field label="Label" name="label"><input name="label" value="{{ old('label', $role->label) }}" class="field"></x-admin.field>
        <x-admin.field label="Description" name="description"><input name="description" value="{{ old('description', $role->description) }}" class="field"></x-admin.field>

        @if($role->name === 'super-admin')
            <p class="rounded-lg bg-amber-50 p-3 text-sm text-amber-800">Super Admin always has every permission.</p>
        @else
            <div class="space-y-4">
                @foreach($permissions as $group => $perms)
                    <div>
                        <p class="mb-1 text-sm font-semibold capitalize text-brand-800">{{ $group }}</p>
                        <div class="grid grid-cols-2 gap-1 text-sm sm:grid-cols-3">
                            @foreach($perms as $perm)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                           @checked(in_array($perm->id, old('permissions', $role->permissions->pluck('id')->all())))
                                           class="rounded border-slate-300 text-brand-500">
                                    {{ \Illuminate\Support\Str::afterLast($perm->name, '.') }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <button class="btn-primary">{{ $role->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
