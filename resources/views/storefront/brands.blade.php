<x-storefront-layout title="Brands" :breadcrumbs="[['label' => 'Brands']]">
    <div class="container-page py-8">
        <h1 class="mb-6 font-display text-2xl font-bold text-brand-800">Brands</h1>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($brands as $brand)
                <a href="{{ route('brands.show', $brand->slug) }}" class="card flex flex-col items-center p-6 text-center hover:shadow-card-hover">
                    <span class="font-display text-lg font-bold text-brand-800">{{ $brand->name }}</span>
                    <span class="mt-1 text-xs text-slate-400">{{ $brand->products_count }} products</span>
                </a>
            @endforeach
        </div>
    </div>
</x-storefront-layout>
