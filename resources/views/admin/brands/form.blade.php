<x-admin-layout :title="$brand->exists ? 'Edit brand' : 'New brand'" active="brands">
    <x-admin.head :title="$brand->exists ? $brand->name : 'New brand'" :back="route('admin.brands.index')" />
    <form method="POST" action="{{ $brand->exists ? route('admin.brands.update', $brand) : route('admin.brands.store') }}"
          enctype="multipart/form-data" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($brand->exists)@method('PUT')@endif
        <x-admin.field label="Name" name="name" :required="true"><input name="name" value="{{ old('name', $brand->name) }}" class="field" required></x-admin.field>
        <x-admin.field label="Slug" name="slug" hint="Leave blank to generate from the name"><input name="slug" value="{{ old('slug', $brand->slug) }}" class="field"></x-admin.field>
        <x-admin.field label="Website" name="website"><input name="website" value="{{ old('website', $brand->website) }}" class="field"></x-admin.field>
        <x-admin.field label="Description" name="description"><textarea name="description" rows="3" class="field">{{ old('description', $brand->description) }}</textarea></x-admin.field>
        <x-admin.field label="Logo" name="logo" hint="Shown in the “Trusted Brands” strip on the homepage. PNG with transparent background works best.">
            @if($brand->logo)<img src="{{ $brand->logoUrl() }}" class="mb-2 h-12 object-contain">@endif
            <input type="file" name="logo" accept="image/*" class="text-sm">
        </x-admin.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Meta title" name="meta_title"><input name="meta_title" value="{{ old('meta_title', $brand->meta_title) }}" class="field"></x-admin.field>
            <x-admin.field label="Meta description" name="meta_description"><input name="meta_description" value="{{ old('meta_description', $brand->meta_description) }}" class="field"></x-admin.field>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $brand->is_active ?? true)) class="rounded border-slate-300 text-brand-500"> Active</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $brand->is_featured)) class="rounded border-slate-300 text-brand-500"> Show in homepage “Trusted Brands”</label>
        </div>
        <x-admin.field label="Homepage position" name="position" hint="Lower shows first in the Trusted Brands strip">
            <input type="number" name="position" value="{{ old('position', $brand->position ?? 0) }}" class="field w-28">
        </x-admin.field>
        <button class="btn-primary">{{ $brand->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
