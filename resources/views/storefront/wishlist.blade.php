<x-storefront-layout title="Your Wishlist" :breadcrumbs="[['label' => 'Wishlist']]">
    <div class="container-page py-8">
        <h1 class="mb-6 font-display text-2xl font-bold text-brand-800">Your Wishlist</h1>

        @if($wishlist->items->isEmpty())
            <div class="card p-12 text-center">
                <p class="text-slate-500">Your wishlist is empty.</p>
                <a href="{{ route('shop.index') }}" class="btn-primary mt-4">Discover products</a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($wishlist->items as $item)
                    @if($item->product)
                        <x-product-card :product="$item->product" :inWishlist="true" />
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</x-storefront-layout>
