<x-storefront-layout :title="'Search: '.$term" :breadcrumbs="[['label' => 'Search']]">
    <div class="container-page py-8">
        <h1 class="font-display text-2xl font-bold text-brand-800">Search results</h1>
        <p class="mt-1 text-sm text-slate-500">
            {{ $products->total() }} result{{ $products->total() === 1 ? '' : 's' }} for “<span class="font-medium text-brand-800">{{ $term }}</span>”
        </p>

        <form method="GET" class="mt-4 flex max-w-lg gap-2">
            <input name="q" value="{{ $term }}" class="field" placeholder="Search again…">
            <select name="sort" class="field w-44 text-sm">
                @foreach($sorts as $value => $label)
                    <option value="{{ $value }}" @selected(request('sort') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn-primary">Search</button>
        </form>

        @if($products->isEmpty())
            <div class="card mt-6 p-12 text-center text-slate-500">
                <p>Nothing matched your search.</p>
                <a href="{{ route('shop.index') }}" class="link mt-2 inline-block text-sm">Browse all products</a>
            </div>
        @else
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        @endif
    </div>
</x-storefront-layout>
