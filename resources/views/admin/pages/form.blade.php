<x-admin-layout :title="$page->exists ? 'Edit page' : 'New page'" active="pages">
    <x-admin.head :title="$page->exists ? $page->title : 'New page'" :back="route('admin.pages.index')" />
    <form method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}"
          enctype="multipart/form-data" class="max-w-3xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($page->exists)@method('PUT')@endif
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Title" name="title"><input name="title" value="{{ old('title', $page->title) }}" class="field"></x-admin.field>
            <x-admin.field label="Slug (optional)" name="slug"><input name="slug" value="{{ old('slug', $page->slug) }}" class="field"></x-admin.field>
        </div>
        <x-admin.field label="Content (HTML)" name="content"><textarea name="content" rows="14" class="field font-mono text-sm">{{ old('content', $page->content) }}</textarea></x-admin.field>
        <x-admin.field label="Featured image" name="featured_image"><input type="file" name="featured_image" accept="image/*" class="text-sm"></x-admin.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Meta title" name="meta_title"><input name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="field"></x-admin.field>
            <x-admin.field label="Meta description" name="meta_description"><input name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" class="field"></x-admin.field>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published ?? true)) class="rounded border-slate-300 text-brand-500"> Published</label>
        <button class="btn-primary">{{ $page->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
