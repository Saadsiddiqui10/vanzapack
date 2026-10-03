<x-admin-layout :title="$rate->exists ? 'Edit tax rate' : 'New tax rate'" active="tax">
    <x-admin.head :title="$rate->exists ? $rate->name : 'New tax rate'" :back="route('admin.tax-rates.index')" />
    <form method="POST" action="{{ $rate->exists ? route('admin.tax-rates.update', $rate) : route('admin.tax-rates.store') }}"
          class="max-w-xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($rate->exists)@method('PUT')@endif
        <x-admin.field label="Name" name="name"><input name="name" value="{{ old('name', $rate->name) }}" class="field"></x-admin.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Country (ISO-2, optional)" name="country"><input name="country" maxlength="2" value="{{ old('country', $rate->country) }}" class="field"></x-admin.field>
            <x-admin.field label="State (optional)" name="state"><input name="state" value="{{ old('state', $rate->state) }}" class="field"></x-admin.field>
            <x-admin.field label="Tax class" name="tax_class"><input name="tax_class" value="{{ old('tax_class', $rate->tax_class ?: 'standard') }}" class="field"></x-admin.field>
            <x-admin.field label="Rate (%)" name="rate"><input type="number" step="0.001" name="rate" value="{{ old('rate', $rate->rate) }}" class="field"></x-admin.field>
            <x-admin.field label="Priority" name="priority"><input type="number" name="priority" value="{{ old('priority', $rate->priority ?? 0) }}" class="field"></x-admin.field>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rate->is_active ?? true)) class="rounded border-slate-300 text-brand-500"> Active</label>
        <button class="btn-primary">{{ $rate->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
