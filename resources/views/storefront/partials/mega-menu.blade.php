@php
    $featuredDeal = \App\Models\Product::active()->whereNotNull('sale_price')
        ->whereColumn('sale_price', '<', 'price')
        ->with('images')->inRandomOrder()->first();
@endphp

<nav class="hidden bg-brand-700 text-white lg:block" aria-label="Primary">
    <div class="container-page">
        <ul class="flex items-center gap-1 text-sm font-medium text-white">
            @forelse($navMenuItems as $menuItem)
                @if($menuItem->isMega())
                    <li x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="static">
                        <button class="flex items-center gap-1 px-3 py-3 transition hover:text-accent" @click="open = !open">
                            {{ $menuItem->label }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute inset-x-0 top-full z-40 max-h-[75vh] overflow-y-auto border-t border-slate-100 bg-white shadow-xl">
                            <div class="container-page grid grid-cols-12 gap-8 py-8">
                                <div class="col-span-9 columns-2 gap-8 xl:columns-3 [column-fill:balance]">
                                    @foreach($navCategories as $parent)
                                        <div class="mb-6 break-inside-avoid">
                                            <a href="{{ route('category.show', $parent->slug) }}"
                                               class="mb-2 block font-display text-sm font-bold uppercase tracking-wide text-brand-800 hover:text-brand-600">
                                                {{ $parent->name }}
                                            </a>
                                            <ul class="space-y-1">
                                                @foreach($parent->children as $section)
                                                    <li>
                                                        <a href="{{ route('category.show', $section->slug) }}"
                                                           class="text-sm {{ $section->children->isNotEmpty() ? 'font-semibold text-slate-700' : 'text-slate-500' }} hover:text-brand-600">
                                                            {{ $section->name }}
                                                        </a>
                                                        @if($section->children->isNotEmpty())
                                                            <ul class="mb-1 ml-3 mt-0.5 space-y-0.5 border-l border-slate-100 pl-3">
                                                                @foreach($section->children as $leaf)
                                                                    <li>
                                                                        <a href="{{ route('category.show', $leaf->slug) }}"
                                                                           class="text-xs text-slate-500 hover:text-brand-600">{{ $leaf->name }}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-span-3">
                                    @if($featuredDeal)
                                        <a href="{{ $featuredDeal->url() }}" class="group block overflow-hidden rounded-xl bg-slate-50">
                                            <img src="{{ $featuredDeal->primaryImageUrl() }}" alt="{{ $featuredDeal->name }}"
                                                 class="aspect-video w-full object-cover transition group-hover:scale-105">
                                            <div class="p-4">
                                                <span class="badge bg-brand-100 text-brand-800">Deal of the day</span>
                                                <p class="mt-1 line-clamp-2 text-sm font-semibold text-brand-800">{{ $featuredDeal->name }}</p>
                                                <p class="mt-1 text-sm text-brand-700">{{ money($featuredDeal->currentPrice()) }}
                                                    <span class="text-slate-400 line-through">{{ money($featuredDeal->price) }}</span>
                                                </p>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @else
                    <li>
                        <a href="{{ $menuItem->href() }}" @if($menuItem->open_in_new_tab) target="_blank" rel="noopener" @endif
                           class="block px-3 py-3 transition hover:text-accent">{{ $menuItem->label }}</a>
                    </li>
                @endif
            @empty
                <li><a href="{{ route('home') }}" class="block px-3 py-3 transition hover:text-accent">Home</a></li>
                <li><a href="{{ route('shop.index') }}" class="block px-3 py-3 transition hover:text-accent">Shop</a></li>
            @endforelse
        </ul>
    </div>
</nav>
