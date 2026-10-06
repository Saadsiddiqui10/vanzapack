@props(['product', 'inWishlist' => false])

@php
    $secondImage = $product->images->get(1);
    $out = ! $product->inStock();
@endphp

<div class="group card relative flex h-full w-full flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-card-hover">
    {{-- Badges --}}
    <div class="absolute left-3 top-3 z-10 flex flex-col gap-1">
        @if($product->isOnSale())
            <span class="badge bg-rose-500 text-white">-{{ $product->discountPercent() }}%</span>
        @endif
        @if($product->is_new_arrival)
            <span class="badge bg-brand-700 text-white">New</span>
        @endif
        @if($out)
            <span class="badge bg-slate-700 text-white">Sold out</span>
        @endif
    </div>

    <button type="button"
            x-data="wishlistButton(@js($product->slug), @js($inWishlist))"
            @click.prevent="toggle()"
            :class="inList ? 'text-rose-500' : 'text-slate-300 hover:text-rose-400'"
            class="absolute right-3 top-3 z-10 rounded-full bg-white/90 p-1.5 shadow transition"
            aria-label="Toggle wishlist">
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21s-7-4.35-9.5-8.5C1 9 3 5.5 6.5 5.5 8.7 5.5 10.5 7 12 9c1.5-2 3.3-3.5 5.5-3.5C21 5.5 23 9 21.5 12.5 19 16.65 12 21 12 21z"/></svg>
    </button>

    {{-- Photos are fitted (not cropped) inside equal padding so every product looks the same size --}}
    <a href="{{ $product->url() }}" class="relative block aspect-square overflow-hidden border-b border-slate-100 bg-white">
        <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" loading="lazy"
             class="h-full w-full object-contain p-5 transition duration-500 {{ $secondImage ? 'group-hover:opacity-0' : 'group-hover:scale-105' }}">
        @if($secondImage)
            <img src="{{ $secondImage->url() }}" alt="" aria-hidden="true" loading="lazy"
                 class="absolute inset-0 h-full w-full object-contain p-5 opacity-0 transition duration-500 group-hover:opacity-100">
        @endif
        <span class="absolute inset-x-3 bottom-3 translate-y-2 opacity-0 transition group-hover:translate-y-0 group-hover:opacity-100">
            <button type="button" @click.prevent="$dispatch('quick-view', @js($product->slug))"
                    class="btn-outline w-full bg-white/95 py-2 text-xs">Quick view</button>
        </span>
    </a>

    <div class="flex flex-1 flex-col p-4">
        @if($product->brand)
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ $product->brand->name }}</p>
        @endif
        {{-- Always reserve two lines so prices and buttons line up across a row --}}
        <a href="{{ $product->url() }}" class="mt-1 line-clamp-2 min-h-[2.5rem] text-sm font-medium leading-5 text-brand-800 hover:text-brand-600">
            {{ $product->name }}
        </a>

        <div class="mt-2">
            <x-rating-stars :rating="$product->rating_avg" :count="$product->reviews_count ?? $product->rating_count" />
        </div>

        {{-- Discount % is already shown as the corner badge --}}
        <div class="mt-2 flex flex-wrap items-baseline gap-x-2">
            <x-price :product="$product" :show-discount="false" />
        </div>

        <div class="mt-auto pt-4">
            @if($product->has_variants)
                <a href="{{ $product->url() }}" class="btn-navy w-full py-2 text-xs">Choose options</a>
            @else
                <form x-data="addToCart(@js($product->id))" @submit.prevent="submit" class="w-full">
                    <button class="btn-navy w-full py-2 text-xs" :disabled="busy || {{ $out ? 'true' : 'false' }}"
                            x-text="busy ? 'Adding…' : '{{ $out ? 'Out of stock' : 'Add to cart' }}'"></button>
                </form>
            @endif
        </div>
    </div>
</div>
