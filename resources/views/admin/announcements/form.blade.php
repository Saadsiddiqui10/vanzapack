<x-admin-layout :title="$announcement->exists ? 'Edit announcement' : 'New announcement'" active="announcements">
    <x-admin.head :title="$announcement->exists ? 'Edit announcement' : 'New announcement'" :back="route('admin.announcements.index')" />

    <form method="POST" action="{{ $announcement->exists ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}"
          class="max-w-xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($announcement->exists)@method('PUT')@endif

        <x-admin.field label="Text" name="text">
            <input name="text" value="{{ old('text', $announcement->text) }}" class="field" maxlength="200" required>
        </x-admin.field>
        <x-admin.field label="Link URL (optional)" name="url" hint="e.g. /shop or https://…  — leave blank for plain text">
            <input name="url" value="{{ old('url', $announcement->url) }}" class="field">
        </x-admin.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Position" name="position" hint="Lower shows first">
                <input type="number" name="position" value="{{ old('position', $announcement->position ?? 0) }}" class="field w-28">
            </x-admin.field>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Show from (optional)" name="starts_at">
                <input type="date" name="starts_at" value="{{ old('starts_at', $announcement->starts_at?->format('Y-m-d')) }}" class="field">
            </x-admin.field>
            <x-admin.field label="Hide after (optional)" name="ends_at">
                <input type="date" name="ends_at" value="{{ old('ends_at', $announcement->ends_at?->format('Y-m-d')) }}" class="field">
            </x-admin.field>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $announcement->is_active ?? true)) class="rounded border-slate-300 text-brand-500"> Active</label>

        <button class="btn-primary">{{ $announcement->exists ? 'Save' : 'Add announcement' }}</button>
    </form>
</x-admin-layout>
