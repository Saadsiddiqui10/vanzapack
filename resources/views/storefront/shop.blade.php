<x-storefront-layout :title="$pageTitle" :metaDescription="$pageDescription"
    :breadcrumbs="array_filter([
        ['label' => 'Shop', 'url' => route('shop.index')],
        ($activeCategory ?? null) ? ['label' => $activeCategory->name] : null,
        ($activeBrand ?? null) ? ['label' => $activeBrand->name] : null,
    ])">

    <div class="container-page py-6" x-data="{ filtersOpen: false }">
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-brand-800">{{ $pageTitle }}</h1>
            @if($pageDescription)<p class="mt-1 text-sm text-slate-500">{{ $pageDescription }}</p>@endif
        </div>

        <div class="lg:grid lg:grid-cols-[16rem_1fr] lg:gap-8">
            {{-- ── Filters ─────────────────────────────── --}}
            <aside class="hidden lg:block">
                @include('storefront.partials.shop-filters')
            </aside>

            {{-- Mobile filter drawer --}}
            <div x-show="filtersOpen" x-cloak class="fixed inset-0 z-50 lg:hidden">
                <div class="absolute inset-0 bg-navy-900/40" @click="filtersOpen = false"></div>
                <div class="absolute left-0 top-0 h-full w-80 max-w-[85%] overflow-y-auto bg-white p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="font-semibold text-brand-800">Filters</span>
                        <button @click="filtersOpen = false" aria-label="Close">✕</button>
                    </div>
                    @include('storefront.partials.shop-filters')
                </div>
            </div>

            {{-- ── Results ─────────────────────────────── --}}
            <div>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 bg-white p-3">
                    <div class="flex items-center gap-3">
                        <button class="btn-outline py-2 lg:hidden" @click="filtersOpen = true">Filters</button>
                        <p class="text-sm text-slate-500">{{ $products->total() }} product{{ $products->total() === 1 ? '' : 's' }}</p>
                    </div>
                    <form method="GET" class="flex items-center gap-2">
                        @foreach(request()->except(['sort', 'page']) as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $v)<input type="hidden" name="{{ $key }}[]" value="{{ $v }}">@endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <label for="sort" class="text-sm text-slate-500">Sort</label>
                        <select id="sort" name="sort" class="field w-48 py-1.5 text-sm" onchange="this.form.submit()">
                            @foreach($sorts as $value => $label)
                                <option value="{{ $value }}" @selected(request('sort') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if($products->isEmpty())
                    <div class="card p-12 text-center text-slate-500">
                        <p>No products match your filters.</p>
                        <a href="{{ route('shop.index') }}" class="link mt-2 inline-block text-sm">Clear all filters</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-storefront-layout>
