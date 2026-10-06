@props(['title', 'products', 'link' => null, 'subtitle' => null])

@if($products->isNotEmpty())
    <section class="container-page py-8">
        <div class="mb-6 flex items-end justify-between">
            <div>
                <x-section-heading :title="$title" />
                @if($subtitle)<p class="text-sm text-slate-500">{{ $subtitle }}</p>@endif
            </div>
            @if($link)<a href="{{ $link }}" class="link text-sm">View all →</a>@endif
        </div>

        <div class="-mx-4 flex snap-x gap-4 overflow-x-auto px-4 pb-4 sm:mx-0 sm:grid sm:grid-cols-3 sm:overflow-visible sm:px-0 lg:grid-cols-5">
            @foreach($products as $product)
                <div class="flex w-56 shrink-0 snap-start sm:w-auto">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>
    </section>
@endif
