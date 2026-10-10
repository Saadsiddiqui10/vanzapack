@php
    $wa = app(\App\Services\WhatsAppService::class);
    $inWishlist = auth()->check() && auth()->user()->wishlist
        ? auth()->user()->wishlist->items->contains('product_id', $product->id)
        : false;
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'image' => $product->images->map(fn ($i) => $i->url())->all(),
        'description' => strip_tags($product->short_description ?? $product->description ?? ''),
        'sku' => $product->sku,
        'brand' => $product->brand ? ['@type' => 'Brand', 'name' => $product->brand->name] : null,
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => settings('currency', 'AED'),
            'price' => number_format($product->currentPrice(), 2, '.', ''),
            'availability' => $product->inStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => $product->url(),
        ],
        'aggregateRating' => $product->rating_count > 0 ? [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) $product->rating_avg,
            'reviewCount' => $product->rating_count,
        ] : null,
    ];
@endphp

<x-storefront-layout
    :title="$product->meta_title ?: $product->name"
    :metaDescription="$product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->short_description), 155)"
    :ogImage="$product->primaryImageUrl()"
    ogType="product"
    :schema="array_filter($schema)"
    :breadcrumbs="array_filter([
        ['label' => 'Shop', 'url' => route('shop.index')],
        $product->category->parent ? ['label' => $product->category->parent->name, 'url' => route('category.show', $product->category->parent->slug)] : null,
        ['label' => $product->category->name, 'url' => route('category.show', $product->category->slug)],
        ['label' => $product->name],
    ])">

    <div class="container-page py-6"
         x-data="variantPicker(@js($product->slug), {
            price: @js(money($product->currentPrice())),
            inStock: @js($product->inStock()),
            stock: @js($product->has_variants ? null : $product->availableStock()),
            image: @js($product->primaryImageUrl()),
            attributeCount: {{ count($variantOptions) }}
         })">
        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Gallery --}}
            <div x-data="{ active: @js($product->primaryImageUrl()) }" x-init="$watch('image', v => v && (active = v))">
                <div class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                    <img :src="active" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
                </div>
                @if($product->images->count() > 1)
                    <div class="mt-3 flex gap-2 overflow-x-auto">
                        @foreach($product->images as $image)
                            <button @click="active = @js($image->url())"
                                    :class="active === @js($image->url()) ? 'ring-2 ring-brand-500' : 'ring-1 ring-slate-200'"
                                    class="h-16 w-16 shrink-0 overflow-hidden rounded-lg">
                                <img src="{{ $image->url() }}" alt="" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div>
                @if($product->brand)
                    <a href="{{ route('brands.show', $product->brand->slug) }}" class="text-sm font-medium uppercase tracking-wide text-brand-600">{{ $product->brand->name }}</a>
                @endif
                <h1 class="mt-1 font-display text-2xl font-bold text-brand-800 md:text-3xl">{{ $product->name }}</h1>

                <div class="mt-2 flex items-center gap-3">
                    <x-rating-stars :rating="$product->rating_avg" :count="$product->rating_count" size="lg" />
                    <a href="#reviews" class="text-sm text-slate-400 hover:text-brand-600">Read reviews</a>
                    <span class="text-sm text-slate-300">|</span>
                    <span class="text-sm text-slate-400">SKU: <span x-text="'{{ $product->sku }}'"></span></span>
                </div>

                <div class="mt-4 flex items-center gap-3 text-2xl">
                    <span class="font-bold text-brand-800" x-text="price"></span>
                    @if($product->isOnSale())
                        <span class="text-lg text-slate-400 line-through">{{ money($product->price) }}</span>
                        <span class="badge bg-rose-100 text-rose-700">Save {{ $product->discountPercent() }}%</span>
                    @endif
                </div>

                @if($product->short_description)
                    <p class="mt-4 text-slate-600">{{ $product->short_description }}</p>
                @endif

                {{-- Variant options --}}
                @foreach($variantOptions as $attrName => $data)
                    <div class="mt-5">
                        <p class="label">{{ $attrName }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($data['values'] as $value)
                                <button type="button"
                                        @click="choose({{ $data['attribute']->id }}, {{ $value->id }})"
                                        :class="isSelected({{ $data['attribute']->id }}, {{ $value->id }}) ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'"
                                        class="rounded-lg border px-3 py-1.5 text-sm">
                                    @if($value->color_hex)
                                        <span class="mr-1.5 inline-block h-3 w-3 rounded-full border" style="background: {{ $value->color_hex }}"></span>
                                    @endif
                                    {{ $value->value }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Stock --}}
                <p class="mt-4 text-sm">
                    <template x-if="inStock">
                        <span class="text-brand-700">✓ In stock<span x-show="stock !== null && stock <= 10" x-text="' — only ' + stock + ' left'"></span></span>
                    </template>
                    <template x-if="!inStock"><span class="text-rose-600">Currently unavailable</span></template>
                </p>

                {{-- Add to cart --}}
                <div class="mt-4 flex flex-wrap items-center gap-3"
                     x-data="{ qty: 1 }">
                    <div class="flex items-center rounded-lg border border-slate-200">
                        <button type="button" class="px-3 py-2 text-slate-500" @click="qty = Math.max(1, qty - 1)">−</button>
                        <input type="number" class="qty-input w-14 border-0 text-center text-sm focus:ring-0" x-model.number="qty" min="1">
                        <button type="button" class="px-3 py-2 text-slate-500" @click="qty++">+</button>
                    </div>

                    <button class="btn-primary flex-1 sm:flex-none sm:px-8"
                            :disabled="!inStock || ({{ $product->has_variants ? 'true' : 'false' }} && !variantId)"
                            @click="
                                $store.cart.loading = true;
                                fetch('{{ route('cart.store') }}', {
                                    method:'POST',
                                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
                                    body: JSON.stringify({ product_id: {{ $product->id }}, variant_id: variantId, quantity: qty })
                                }).then(r => r.json()).then(d => {
                                    window.vpToast(d.message, d.ok ? 'success' : 'error');
                                    if (d.ok) { $store.cart.setCount(d.cart_count); $store.cart.openDrawer(); }
                                });
                            ">
                        Add to Cart
                    </button>

                    <a href="{{ route('checkout.index') }}" class="btn-navy" x-show="inStock">Buy Now</a>
                </div>

                <div class="mt-3 flex flex-wrap gap-3 text-sm">
                    <button type="button"
                            x-data="wishlistButton(@js($product->slug), @js($inWishlist))" @click="toggle()"
                            class="inline-flex items-center gap-1.5 text-slate-500 hover:text-rose-500">
                        <svg class="h-4 w-4" :fill="inList ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 21s-7-4.35-9.5-8.5C1 9 3 5.5 6.5 5.5 8.7 5.5 10.5 7 12 9c1.5-2 3.3-3.5 5.5-3.5C21 5.5 23 9 21.5 12.5 19 16.65 12 21 12 21z"/></svg>
                        <span x-text="inList ? 'Saved to wishlist' : 'Add to wishlist'"></span>
                    </button>

                    @if($wa->enabledForProduct())
                        <button type="button" data-wa-href="{{ $wa->productLink($product) }}"
                           class="inline-flex items-center gap-1.5 text-[#128C7E] hover:underline">
                            💬 Order / Enquire on WhatsApp
                        </button>
                    @endif
                </div>

                {{-- Meta --}}
                <dl class="mt-6 grid grid-cols-2 gap-2 border-t border-slate-100 pt-4 text-sm text-slate-500">
                    <div><dt class="inline font-medium text-brand-800">Category:</dt> <dd class="inline">{{ $product->category->name }}</dd></div>
                    @if($product->tags)<div><dt class="inline font-medium text-brand-800">Tags:</dt> <dd class="inline">{{ implode(', ', $product->tags) }}</dd></div>@endif
                </dl>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="mt-12" x-data="{ tab: 'description' }">
            <div class="flex gap-6 border-b border-slate-200 text-sm font-medium">
                @foreach(['description' => 'Description', 'specifications' => 'Specifications', 'shipping' => 'Shipping & Returns', 'reviews' => 'Reviews ('.$product->rating_count.')'] as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-brand-500 text-brand-800' : 'border-transparent text-slate-400'"
                            class="-mb-px border-b-2 pb-3">{{ $label }}</button>
                @endforeach
            </div>

            <div class="py-6">
                <div x-show="tab === 'description'" class="prose-cms">{!! $product->description !!}</div>
                <div x-show="tab === 'specifications'" x-cloak class="prose-cms">{!! $product->specifications ?: '<p>No specifications listed.</p>' !!}</div>
                <div x-show="tab === 'shipping'" x-cloak class="prose-cms">
                    {!! $product->shipping_info !!}
                    {!! $product->return_info !!}
                </div>
                <div x-show="tab === 'reviews'" x-cloak id="reviews">
                    @include('storefront.partials.product-reviews')
                </div>
            </div>
        </div>

        {{-- Related --}}
        <x-storefront.product-row title="Related Products" :products="$related" />
        @if($recentlyViewed->isNotEmpty())
            <x-storefront.product-row title="Recently Viewed" :products="$recentlyViewed" />
        @endif
    </div>
</x-storefront-layout>
