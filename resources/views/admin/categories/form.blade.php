<x-admin-layout :title="$category->exists ? 'Edit category' : 'New category'" active="categories">
    <x-admin.head :title="$category->exists ? $category->name : 'New category'" :back="route('admin.categories.index')" />

    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
          enctype="multipart/form-data" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($category->exists)@method('PUT')@endif

        <x-admin.field label="Name" name="name" :required="true"><input name="name" value="{{ old('name', $category->name) }}" class="field" required></x-admin.field>
        <x-admin.field label="Slug" name="slug" hint="Leave blank to generate from the name"><input name="slug" value="{{ old('slug', $category->slug) }}" class="field"></x-admin.field>
        <x-admin.field label="Parent category" name="parent_id">
            <select name="parent_id" class="field">
                <option value="">— Top level —</option>
                @foreach($parents as $p)<option value="{{ $p->id }}" @selected(old('parent_id', $category->parent_id) == $p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </x-admin.field>
        <x-admin.field label="Description" name="description"><textarea name="description" rows="3" class="field">{{ old('description', $category->description) }}</textarea></x-admin.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Image" name="image">
                @if($category->image)<img src="{{ $category->imageUrl() }}" class="mb-2 h-16 rounded object-cover">@endif
                <input type="file" name="image" accept="image/*" class="text-sm">
            </x-admin.field>
            <x-admin.field label="Banner" name="banner"><input type="file" name="banner" accept="image/*" class="text-sm"></x-admin.field>
        </div>
        <x-admin.field label="Position" name="position"><input type="number" name="position" value="{{ old('position', $category->position ?? 0) }}" class="field w-32"></x-admin.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Meta title" name="meta_title"><input name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" class="field"></x-admin.field>
            <x-admin.field label="Meta description" name="meta_description"><input name="meta_description" value="{{ old('meta_description', $category->meta_description) }}" class="field"></x-admin.field>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) class="rounded border-slate-300 text-brand-500"> Active</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $category->is_featured)) class="rounded border-slate-300 text-brand-500"> Featured on homepage</label>

        <button class="btn-primary">{{ $category->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
