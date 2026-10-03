<div class="grid gap-6 sm:grid-cols-2">
    <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" class="aspect-square w-full rounded-xl object-cover">
    <div>
        @if($product->brand)<p class="text-xs font-medium uppercase tracking-wide text-brand-600">{{ $product->brand->name }}</p>@endif
        <h2 class="mt-1 font-display text-xl font-bold text-brand-800">{{ $product->name }}</h2>
        <div class="mt-2"><x-rating-stars :rating="$product->rating_avg" :count="$product->rating_count" /></div>
        <div class="mt-3 text-xl"><x-price :product="$product" /></div>
        <p class="mt-3 text-sm text-slate-600">{{ $product->short_description }}</p>

        <div class="mt-5 flex gap-3">
            @if($product->has_variants)
                <a href="{{ $product->url() }}" class="btn-navy flex-1">Choose options</a>
            @else
                <form x-data="addToCart(@js($product->id))" @submit.prevent="submit(); $root.closest('[x-data]')" class="flex-1">
                    <button class="btn-primary w-full" :disabled="busy" x-text="busy ? 'Adding…' : 'Add to cart'"></button>
                </form>
            @endif
            <a href="{{ $product->url() }}" class="btn-outline">Details</a>
        </div>
    </div>
</div>
