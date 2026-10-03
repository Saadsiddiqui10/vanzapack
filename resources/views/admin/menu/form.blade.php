<x-admin-layout :title="$item->exists ? 'Edit menu item' : 'New menu item'" active="menu">
    <x-admin.head :title="$item->exists ? 'Edit menu item' : 'New menu item'" :back="route('admin.menu-items.index')" />

    <form method="POST" action="{{ $item->exists ? route('admin.menu-items.update', $item) : route('admin.menu-items.store') }}"
          x-data="{ type: '{{ old('type', $item->type ?? 'link') }}' }"
          class="max-w-xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($item->exists)@method('PUT')@endif

        <x-admin.field label="Label" name="label">
            <input name="label" value="{{ old('label', $item->label) }}" class="field" maxlength="60" required>
        </x-admin.field>

        <x-admin.field label="Type" name="type">
            <select name="type" x-model="type" class="field">
                <option value="link">Link</option>
                <option value="mega">Category dropdown (mega-menu)</option>
            </select>
        </x-admin.field>

        <div x-show="type === 'link'">
            <x-admin.field label="Link URL" name="url" hint="Site path (e.g. /shop, /offers, /page/about-us) or full https:// URL">
                <input name="url" value="{{ old('url', $item->url) }}" class="field">
            </x-admin.field>
            <label class="mt-2 flex items-center gap-2 text-sm">
                <input type="checkbox" name="open_in_new_tab" value="1" @checked(old('open_in_new_tab', $item->open_in_new_tab)) class="rounded border-slate-300 text-brand-500"> Open in new tab
            </label>
        </div>

        <x-admin.field label="Position" name="position" hint="Lower shows first (left)">
            <input type="number" name="position" value="{{ old('position', $item->position ?? 0) }}" class="field w-28">
        </x-admin.field>

        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true)) class="rounded border-slate-300 text-brand-500"> Visible</label>

        <button class="btn-primary">{{ $item->exists ? 'Save' : 'Add menu item' }}</button>
    </form>
</x-admin-layout>
