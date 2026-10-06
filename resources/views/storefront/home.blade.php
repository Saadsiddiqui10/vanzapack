<x-storefront-layout>
    {{-- ── Hero (full-width slider) ─────────────────────────── --}}
    <section x-data="{ i: 0, count: {{ max(1, $heroBanners->count()) }} }"
             x-init="count > 1 && setInterval(() => i = (i + 1) % count, 6000)"
             class="relative w-full overflow-hidden bg-gradient-to-br from-brand-50 to-brand-100
                    h-[62vw] max-h-[560px] min-h-[340px] sm:h-[46vw] lg:h-[38vw]">

        @forelse($heroBanners as $index => $banner)
            <div x-show="i === {{ $index }}"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-500 absolute"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute inset-0">
                {{-- full-bleed background image --}}
                <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->title }}"
                     class="absolute inset-0 h-full w-full object-cover">
                {{-- readability wash on the text side --}}
                <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-white/10 md:to-transparent"></div>

                <div class="container-page relative flex h-full flex-col justify-center">
                    <div class="max-w-xl">
                        <h1 class="font-display text-2xl font-extrabold leading-tight text-brand-800 sm:text-3xl lg:text-5xl">{{ $banner->title }}</h1>
                        <p class="mt-3 max-w-md text-sm text-slate-600 sm:text-base">{{ $banner->subtitle }}</p>
                        <div class="mt-5 flex flex-wrap gap-3 sm:mt-7">
                            <a href="{{ $banner->cta_url ?: route('shop.index') }}" class="btn-primary">{{ $banner->cta_label ?: 'Shop Now' }}</a>
                            <a href="{{ route('shop.new') }}" class="btn-outline">Explore Collection</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="absolute inset-0">
                <img src="https://placehold.co/1600x700/e6f2cf/000066?text=VanzaPack" alt="" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-white/10 md:to-transparent"></div>
                <div class="container-page relative flex h-full flex-col justify-center">
                    <div class="max-w-xl">
                        <h1 class="font-display text-2xl font-extrabold text-brand-800 sm:text-3xl lg:text-5xl">Your Destination for Quality Packaging</h1>
                        <p class="mt-3 max-w-md text-sm text-slate-600 sm:text-base">Bagasse, cups, tissue and grocery essentials — everything your business needs in one place.</p>
                        <a href="{{ route('shop.index') }}" class="btn-primary mt-5">Shop Now</a>
                    </div>
                </div>
            </div>
        @endforelse

        @if($heroBanners->count() > 1)
            <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                @foreach($heroBanners as $index => $b)
                    <button @click="i = {{ $index }}" :class="i === {{ $index }} ? 'bg-brand-700 w-6' : 'bg-white/70'"
                            class="h-2 w-2 rounded-full shadow transition-all" aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ── Value props ──────────────────────────────────────── --}}
    <section class="container-page grid grid-cols-2 gap-4 py-6 lg:grid-cols-4">
        @foreach([
            ['🚚', 'Fast UAE Delivery', 'On-time across all Emirates'],
            ['🔒', 'Secure Payments', 'COD & bank transfer'],
            ['↩️', 'Easy Returns', '7-day return window'],
            ['💬', 'Business Support', 'Help choosing the right products'],
        ] as [$icon, $title, $sub])
            <div class="card flex items-center gap-3 p-4">
                <span class="text-2xl">{{ $icon }}</span>
                <div>
                    <p class="text-sm font-semibold text-brand-800">{{ $title }}</p>
                    <p class="text-xs text-slate-400">{{ $sub }}</p>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ── Shop by Category + Trusted Brands (auto-scrolling) ── --}}
    @if($featuredCategories->isNotEmpty() || $brands->isNotEmpty())
        <section class="py-8">
            @if($featuredCategories->isNotEmpty())
                <div class="container-page mb-3 flex items-end justify-between">
                    <x-section-heading title="Shop by Category" />
                    <a href="{{ route('shop.index') }}" class="link text-sm">View all →</a>
                </div>
                <div class="marquee mb-8 py-1" style="--marquee-duration: {{ max(24, $featuredCategories->count() * 6) }}s; --marquee-gap: 1rem;">
                    @foreach([1, 2] as $pass)
                        <div class="marquee__track" aria-hidden="{{ $pass === 2 ? 'true' : 'false' }}">
                            @foreach($featuredCategories as $category)
                                <a href="{{ route('category.show', $category->slug) }}"
                                   class="card group w-52 shrink-0 overflow-hidden text-center transition hover:-translate-y-1 hover:shadow-card-hover">
                                    <div class="aspect-video overflow-hidden bg-slate-50">
                                        <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}"
                                             class="h-full w-full object-cover transition group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-3">
                                        <p class="truncate text-sm font-semibold text-brand-800" title="{{ $category->name }}">{{ $category->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $category->products_count }} products</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif

            @if($brands->isNotEmpty())
                <div class="container-page mb-3 flex items-end justify-between">
                    <x-section-heading title="Trusted Brands" />
                    <a href="{{ route('brands.index') }}" class="link text-sm">All brands →</a>
                </div>
                <div class="marquee py-1" style="--marquee-duration: {{ max(20, $brands->count() * 5) }}s; --marquee-gap: 1rem;">
                    @foreach([1, 2] as $pass)
                        <div class="marquee__track" aria-hidden="{{ $pass === 2 ? 'true' : 'false' }}">
                            @foreach($brands as $brand)
                                <a href="{{ route('brands.show', $brand->slug) }}"
                                   class="flex h-24 w-40 shrink-0 items-center justify-center rounded-xl border border-slate-100 bg-white p-4 transition hover:border-brand-500 hover:shadow-card">
                                    @if($brand->logo)
                                        <img src="{{ $brand->logoUrl() }}" alt="{{ $brand->name }}" class="max-h-14 max-w-full object-contain" loading="lazy">
                                    @else
                                        <span class="text-center text-sm font-semibold text-brand-800">{{ $brand->name }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- ── Mega deals ───────────────────────────────────────── --}}
    @if($dealProducts->isNotEmpty())
        <section class="bg-brand-700 py-10">
            <div class="container-page">
                <div class="mb-6 flex items-center justify-between text-white">
                    <x-section-heading title="🔥 Mega Deals" first="text-brand-300" rest="text-white" />
                    <a href="{{ route('shop.offers') }}" class="text-sm text-brand-300 hover:text-brand-200">View all →</a>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach($dealProducts->take(5) as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ── New arrivals ─────────────────────────────────────── --}}
    <x-storefront.product-row title="New Arrivals" :products="$newArrivals" :link="route('shop.new')" />

    {{-- ── Promo banner ─────────────────────────────────────── --}}
    @if($promoBanners->isNotEmpty())
        <section class="container-page py-4">
            @foreach($promoBanners->take(1) as $promo)
                <a href="{{ $promo->cta_url ?: route('shop.offers') }}"
                   class="block overflow-hidden rounded-2xl">
                    <img src="{{ $promo->imageUrl() }}" alt="{{ $promo->title }}" class="w-full object-cover">
                </a>
            @endforeach
        </section>
    @endif

    {{-- ── Best sellers ─────────────────────────────────────── --}}
    <x-storefront.product-row title="Best Sellers" :products="$bestSellers" :link="route('shop.best')" />

    {{-- ── Featured products ────────────────────────────────── --}}
    <x-storefront.product-row title="Featured Products" :products="$featuredProducts" :link="route('shop.index')" />

    {{-- ── Why choose us ────────────────────────────────────── --}}
    <section class="bg-brand-50 py-12">
        <div class="container-page">
            <x-section-heading :title="'Why Choose '.settings('store_name', 'VanzaPack')" />
            <div class="mt-6 grid gap-4 md:grid-cols-4">
                @foreach([
                    ['Quality Assurance', 'Premium materials and strict quality checks you can trust.'],
                    ['Competitive Pricing', 'Best value for your business with bulk discounts.'],
                    ['Fast Delivery Across UAE', 'Reliable and on-time delivery across all Emirates.'],
                    ['Business Support', 'Helping businesses choose the right products easily.'],
                ] as [$t, $d])
                    <div class="rounded-xl bg-white p-5 shadow-card">
                        <p class="font-semibold text-brand-700">{{ $t }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $d }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Testimonials ─────────────────────────────────────── --}}
    <section class="bg-slate-50 py-12">
        <div class="container-page">
            <p class="text-xs font-semibold uppercase tracking-widest text-brand-600">Customer Stories</p>
            <x-section-heading title="What Our Customers Say" class="mt-1" />
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                @foreach([
                    ['Fatima Zahra', 'Cafe Manager, Abu Dhabi', 'Impressed with the product variety and competitive pricing. Everything we need with great quality and timely delivery.'],
                    ['Mohammed Tariq', 'Retail Business Owner, Sharjah', 'A dependable supplier for business essentials. Their wide range and professional support make ordering simple.'],
                    ['Sarah Mitchell', 'Hotel Procurement Manager, Dubai', 'Quality, consistency and service standards are excellent. Helps us maintain high standards every day.'],
                ] as [$name, $role, $quote])
                    <figure class="card p-6">
                        <div class="text-amber-400">★★★★★</div>
                        <blockquote class="mt-2 text-sm text-slate-600">“{{ $quote }}”</blockquote>
                        <figcaption class="mt-4 text-sm">
                            <span class="font-semibold text-brand-800">{{ $name }}</span><br>
                            <span class="text-slate-400">{{ $role }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

</x-storefront-layout>
