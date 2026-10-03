<x-admin-layout :title="$attribute->exists ? 'Edit attribute' : 'New attribute'" active="attributes">
    <x-admin.head :title="$attribute->exists ? $attribute->name : 'New attribute'" :back="route('admin.attributes.index')" />
    <form method="POST" action="{{ $attribute->exists ? route('admin.attributes.update', $attribute) : route('admin.attributes.store') }}"
          class="max-w-xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($attribute->exists)@method('PUT')@endif
        <x-admin.field label="Name" name="name"><input name="name" value="{{ old('name', $attribute->name) }}" class="field"></x-admin.field>
        <x-admin.field label="Type" name="type">
            <select name="type" class="field">
                @foreach(['select' => 'Select', 'color' => 'Colour swatch', 'text' => 'Text'] as $v => $l)
                    <option value="{{ $v }}" @selected(old('type', $attribute->type) === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </x-admin.field>
        <x-admin.field label="Values (one per line, optional |#hex for colours)" name="values_text" hint="e.g. Small  or  Kraft|#b0854a">
            <textarea name="values_text" rows="6" class="field">{{ old('values_text', $attribute->values->map(fn($v) => $v->value.($v->color_hex ? '|'.$v->color_hex : ''))->implode("\n")) }}</textarea>
        </x-admin.field>
        <x-admin.field label="Position" name="position"><input type="number" name="position" value="{{ old('position', $attribute->position ?? 0) }}" class="field w-32"></x-admin.field>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_variation" value="1" @checked(old('is_variation', $attribute->is_variation ?? true)) class="rounded border-slate-300 text-brand-500"> Used for product variations &amp; filters</label>
        <button class="btn-primary">{{ $attribute->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
