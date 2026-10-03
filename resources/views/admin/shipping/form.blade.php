<x-admin-layout :title="$method->exists ? 'Edit method' : 'New method'" active="shipping">
    <x-admin.head :title="$method->exists ? $method->name : 'New shipping method'" :back="route('admin.shipping-methods.index')" />
    <form method="POST" action="{{ $method->exists ? route('admin.shipping-methods.update', $method) : route('admin.shipping-methods.store') }}"
          class="max-w-xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($method->exists)@method('PUT')@endif
        <x-admin.field label="Name" name="name"><input name="name" value="{{ old('name', $method->name) }}" class="field"></x-admin.field>
        <x-admin.field label="Description" name="description"><input name="description" value="{{ old('description', $method->description) }}" class="field"></x-admin.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Zone" name="zone"><input name="zone" value="{{ old('zone', $method->zone) }}" class="field"></x-admin.field>
            <x-admin.field label="Country (ISO-2, optional)" name="country"><input name="country" value="{{ old('country', $method->country) }}" class="field" maxlength="2"></x-admin.field>
            <x-admin.field label="Price" name="price"><input type="number" step="0.01" name="price" value="{{ old('price', $method->price) }}" class="field"></x-admin.field>
            <x-admin.field label="Free shipping threshold" name="free_shipping_threshold"><input type="number" step="0.01" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $method->free_shipping_threshold) }}" class="field"></x-admin.field>
            <x-admin.field label="Estimated delivery" name="estimated_delivery"><input name="estimated_delivery" value="{{ old('estimated_delivery', $method->estimated_delivery) }}" class="field"></x-admin.field>
            <x-admin.field label="Position" name="position"><input type="number" name="position" value="{{ old('position', $method->position ?? 0) }}" class="field"></x-admin.field>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $method->is_active ?? true)) class="rounded border-slate-300 text-brand-500"> Active</label>
        <button class="btn-primary">{{ $method->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
