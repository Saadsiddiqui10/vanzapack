<x-admin-layout :title="$coupon->exists ? 'Edit coupon' : 'New coupon'" active="coupons">
    <x-admin.head :title="$coupon->exists ? $coupon->code : 'New coupon'" :back="route('admin.coupons.index')" />
    <form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}"
          x-data="{ scope: '{{ old('scope', $coupon->scope->value ?? 'global') }}' }"
          class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if($coupon->exists)@method('PUT')@endif
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field label="Code" name="code"><input name="code" value="{{ old('code', $coupon->code) }}" class="field font-mono uppercase"></x-admin.field>
            <x-admin.field label="Description" name="description"><input name="description" value="{{ old('description', $coupon->description) }}" class="field"></x-admin.field>
            <x-admin.field label="Type" name="type">
                <select name="type" class="field">
                    <option value="percentage" @selected(old('type', $coupon->type->value ?? 'percentage') === 'percentage')>Percentage</option>
                    <option value="fixed" @selected(old('type', $coupon->type->value ?? '') === 'fixed')>Fixed amount</option>
                </select>
            </x-admin.field>
            <x-admin.field label="Value" name="value"><input type="number" step="0.01" name="value" value="{{ old('value', $coupon->value) }}" class="field"></x-admin.field>
            <x-admin.field label="Minimum order total" name="min_order_total"><input type="number" step="0.01" name="min_order_total" value="{{ old('min_order_total', $coupon->min_order_total) }}" class="field"></x-admin.field>
            <x-admin.field label="Max discount" name="max_discount"><input type="number" step="0.01" name="max_discount" value="{{ old('max_discount', $coupon->max_discount) }}" class="field"></x-admin.field>
            <x-admin.field label="Usage limit" name="usage_limit"><input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="field"></x-admin.field>
            <x-admin.field label="Per-user limit" name="per_user_limit"><input type="number" name="per_user_limit" value="{{ old('per_user_limit', $coupon->per_user_limit) }}" class="field"></x-admin.field>
            <x-admin.field label="Starts at" name="starts_at"><input type="date" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d')) }}" class="field"></x-admin.field>
            <x-admin.field label="Expires at" name="expires_at"><input type="date" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d')) }}" class="field"></x-admin.field>
        </div>

        <x-admin.field label="Scope" name="scope">
            <select name="scope" x-model="scope" class="field">
                <option value="global">All products</option>
                <option value="product">Specific products</option>
                <option value="category">Specific categories</option>
            </select>
        </x-admin.field>

        <div x-show="scope === 'product'" x-cloak>
            <x-admin.field label="Product SKUs (comma separated)" name="product_ids_text">
                <input name="product_ids_text" value="{{ old('product_ids_text', collect($coupon->product_ids)->map(fn($id) => \App\Models\Product::find($id)?->sku)->filter()->implode(', ')) }}" class="field">
            </x-admin.field>
        </div>
        <div x-show="scope === 'category'" x-cloak>
            <label class="label">Categories</label>
            <div class="grid max-h-40 grid-cols-2 gap-1 overflow-y-auto rounded border border-slate-200 p-2 text-sm">
                @foreach($categories as $cat)
                    <label class="flex items-center gap-2"><input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" @checked(in_array($cat->id, (array) old('category_ids', $coupon->category_ids ?? []))) class="rounded border-slate-300 text-brand-500"> {{ $cat->name }}</label>
                @endforeach
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="rounded border-slate-300 text-brand-500"> Active</label>
        <button class="btn-primary">{{ $coupon->exists ? 'Save' : 'Create' }}</button>
    </form>
</x-admin-layout>
