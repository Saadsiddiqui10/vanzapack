<x-admin-layout :title="$banner->exists ? 'Edit banner' : 'New banner'" active="banners">
    <x-admin.head :title="$banner->exists ? ($banner->title ?: 'Banner') : 'New banner'" :back="route('admin.banners.index')" />
    <form method="POST" action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
          enctype="multipart/form-data" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($banner->exists)@method('PUT')@endif
        <x-admin.field label="Placement" name="placement">
            <select name="placement" class="field">
                @foreach(['hero' => 'Hero slider', 'promo' => 'Promo strip', 'category' => 'Category'] as $v => $l)
                    <option value="{{ $v }}" @selected(old('placement', $banner->placement) === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </x-admin.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Title" name="title"><input name="title" value="{{ old('title', $banner->title) }}" class="field"></x-admin.field>
            <x-admin.field label="Subtitle" name="subtitle"><input name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" class="field"></x-admin.field>
            <x-admin.field label="CTA label" name="cta_label"><input name="cta_label" value="{{ old('cta_label', $banner->cta_label) }}" class="field"></x-admin.field>
            <x-admin.field label="CTA URL" name="cta_url"><input name="cta_url" value="{{ old('cta_url', $banner->cta_url) }}" class="field"></x-admin.field>
            <x-admin.field label="Starts at" name="starts_at"><input type="date" name="starts_at" value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d')) }}" class="field"></x-admin.field>
            <x-admin.field label="Ends at" name="ends_at"><input type="date" name="ends_at" value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d')) }}" class="field"></x-admin.field>
        </div>
        <x-admin.field label="Image (1600×640)" name="image">
            @if($banner->image)<img src="{{ $banner->imageUrl() }}" class="mb-2 h-24 rounded object-cover">@endif
            <input type="file" name="image" accept="image/*" class="text-sm">
        </x-admin.field>
        <x-admin.field label="Mobile image (optional)" name="mobile_image"><input type="file" name="mobile_image" accept="image/*" class="text-sm"></x-admin.field>
        <x-admin.field label="Position" name="position"><input type="number" name="position" value="{{ old('position', $banner->position ?? 0) }}" class="field w-32"></x-admin.field>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true)) class="rounded border-slate-300 text-brand-500"> Active</label>
        <button class="btn-primary">{{ $banner->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
