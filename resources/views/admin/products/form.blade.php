<x-admin-layout :title="$product->exists ? 'Edit product' : 'New product'" active="products">
    <x-admin.head :title="$product->exists ? $product->name : 'New product'" :back="route('admin.products.index')" />

    <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
          enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if($product->exists)@method('PUT')@endif

        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-4 font-semibold text-brand-800">Details</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-admin.field label="Name" name="name" :required="true" class="sm:col-span-2">
                        <input name="name" value="{{ old('name', $product->name) }}" class="field" required>
                    </x-admin.field>
                    <x-admin.field label="SKU" name="sku" :required="true">
                        <input name="sku" value="{{ old('sku', $product->sku) }}" class="field" required>
                    </x-admin.field>
                    <x-admin.field label="Barcode" name="barcode">
                        <input name="barcode" value="{{ old('barcode', $product->barcode) }}" class="field">
                    </x-admin.field>
                    <x-admin.field label="Category" name="category_id" :required="true">
                        <select name="category_id" class="field" required>
                            <option value="" disabled @selected(! old('category_id', $product->category_id))>— Select a category —</option>
                            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach
                        </select>
                    </x-admin.field>
                    <x-admin.field label="Short description" name="short_description" class="sm:col-span-2">
                        <textarea name="short_description" rows="2" class="field">{{ old('short_description', $product->short_description) }}</textarea>
                    </x-admin.field>
                    <x-admin.field label="Description (HTML allowed)" name="description" class="sm:col-span-2">
                        <textarea name="description" rows="6" class="field">{{ old('description', $product->description) }}</textarea>
                    </x-admin.field>
                    <x-admin.field label="Specifications" name="specifications" class="sm:col-span-2">
                        <textarea name="specifications" rows="3" class="field">{{ old('specifications', $product->specifications) }}</textarea>
                    </x-admin.field>
                    <x-admin.field label="Shipping info" name="shipping_info">
                        <textarea name="shipping_info" rows="3" class="field">{{ old('shipping_info', $product->shipping_info) }}</textarea>
                    </x-admin.field>
                    <x-admin.field label="Return info" name="return_info">
                        <textarea name="return_info" rows="3" class="field">{{ old('return_info', $product->return_info) }}</textarea>
                    </x-admin.field>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-4 font-semibold text-brand-800">Pricing</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <x-admin.field label="Price" name="price" :required="true"><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" class="field" required></x-admin.field>
                    <x-admin.field label="Sale price" name="sale_price"><input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="field"></x-admin.field>
                    <x-admin.field label="Cost price" name="cost_price"><input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="field"></x-admin.field>
                    <x-admin.field label="Tax class" name="tax_class" :required="true"><input name="tax_class" value="{{ old('tax_class', $product->tax_class ?: 'standard') }}" class="field" required></x-admin.field>
                    <x-admin.field label="Weight (kg)" name="weight"><input type="number" step="0.001" name="weight" value="{{ old('weight', $product->weight) }}" class="field"></x-admin.field>
                </div>
            </div>

            @unless($product->has_variants)
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="mb-4 font-semibold text-brand-800">Inventory</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-admin.field label="Stock quantity"><input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->inventory?->quantity ?? 0) }}" class="field"></x-admin.field>
                        <x-admin.field label="Low-stock threshold"><input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->inventory?->low_stock_threshold ?? 5) }}" class="field"></x-admin.field>
                    </div>
                    <label class="mt-3 flex items-center gap-2 text-sm"><input type="checkbox" name="track_inventory" value="1" @checked(old('track_inventory', $product->track_inventory ?? true)) class="rounded border-slate-300 text-brand-500"> Track inventory</label>
                    <label class="mt-1 flex items-center gap-2 text-sm"><input type="checkbox" name="allow_backorder" value="1" @checked(old('allow_backorder', $product->allow_backorder)) class="rounded border-slate-300 text-brand-500"> Allow backorder</label>
                </div>
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    This product has {{ $product->variants->count() }} variants. Manage per-variant stock in
                    <a href="{{ route('admin.inventory.index', ['q' => $product->sku]) }}" class="underline">Inventory</a>.
                </div>
            @endunless

            <div class="rounded-xl border border-slate-200 bg-white p-5"
                 x-data="{ deleting: null }">
                <h3 class="mb-4 font-semibold text-brand-800">Images</h3>

                @if($product->exists && $product->images->isNotEmpty())
                    <div class="mb-4 grid grid-cols-3 gap-3 sm:grid-cols-5">
                        @foreach($product->images as $image)
                            <div class="relative" :class="deleting === {{ $image->id }} && 'opacity-40 pointer-events-none'">
                                <img src="{{ $image->url() }}" alt="" class="aspect-square w-full rounded-lg object-cover {{ $image->is_primary ? 'ring-2 ring-brand-500' : 'ring-1 ring-slate-200' }}">
                                @if($image->is_primary)
                                    <span class="absolute left-1 top-1 rounded bg-brand-600 px-1 text-[10px] font-semibold text-white">Primary</span>
                                @endif
                                <button type="button"
                                        @click="if (confirm('Remove this image?')) {
                                            deleting = {{ $image->id }};
                                            fetch('{{ route('admin.products.images.destroy', $image) }}', {
                                                method: 'DELETE',
                                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
                                            }).then(() => window.location.reload());
                                        }"
                                        class="absolute right-1 top-1 rounded-full bg-white px-1.5 text-xs text-rose-600 shadow hover:bg-rose-50">✕</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <input type="file" name="images[]" multiple accept="image/*"
                       class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700">
                <p class="mt-1 text-xs text-slate-400">
                    JPG, PNG or WebP, up to 8&nbsp;MB each. The first image uploaded becomes the primary image.
                    @unless($product->exists) You can also add more images after saving. @endunless
                </p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-3 font-semibold text-brand-800">Visibility</h3>
                <x-admin.field label="Status" name="status" :required="true">
                    <select name="status" class="field" required>
                        @foreach(['active' => 'Active', 'draft' => 'Draft', 'archived' => 'Archived'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('status', $product->status->value ?? 'active') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </x-admin.field>
                <div class="mt-3 space-y-2 text-sm">
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="rounded border-slate-300 text-brand-500"> Featured</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_new_arrival" value="1" @checked(old('is_new_arrival', $product->is_new_arrival)) class="rounded border-slate-300 text-brand-500"> New arrival</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_best_seller" value="1" @checked(old('is_best_seller', $product->is_best_seller)) class="rounded border-slate-300 text-brand-500"> Best seller</label>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-3 font-semibold text-brand-800">SEO &amp; tags</h3>
                <x-admin.field label="Meta title" name="meta_title"><input name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" class="field"></x-admin.field>
                <x-admin.field label="Meta description" name="meta_description" class="mt-3"><textarea name="meta_description" rows="3" class="field">{{ old('meta_description', $product->meta_description) }}</textarea></x-admin.field>
                <x-admin.field label="Tags (comma separated)" name="tags" class="mt-3"><input name="tags" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : '') }}" class="field"></x-admin.field>
            </div>

            <div class="flex gap-2">
                <button class="btn-primary flex-1">{{ $product->exists ? 'Save changes' : 'Create product' }}</button>
                @if($product->exists)
                    <button form="delete-product" class="btn-outline text-rose-600">Delete</button>
                @endif
            </div>
        </div>
    </form>

    @if($product->exists)
        <form id="delete-product" method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Archive this product?')">
            @csrf @method('DELETE')
        </form>
    @endif
</x-admin-layout>
