<form method="GET" class="space-y-6 text-sm" x-data>
    {{-- keep search term --}}
    @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

    <div>
        <h3 class="mb-2 font-semibold text-brand-800">Categories</h3>
        <ul class="space-y-1">
            @foreach($filterCategories as $cat)
                <li>
                    <a href="{{ route('category.show', $cat->slug) }}"
                       class="block rounded px-2 py-1 {{ ($activeCategory?->id === $cat->id) ? 'bg-brand-50 font-semibold text-brand-700' : 'hover:bg-slate-50' }}">
                        {{ $cat->name }}
                    </a>
                    @if($cat->children->isNotEmpty())
                        <ul class="ml-3 mt-1 space-y-1 border-l border-slate-100 pl-3">
                            @foreach($cat->children as $child)
                                <li>
                                    <a href="{{ route('category.show', $child->slug) }}"
                                       class="block rounded px-2 py-0.5 text-slate-500 {{ ($activeCategory?->id === $child->id) ? 'font-semibold text-brand-700' : 'hover:text-brand-600' }}">
                                        {{ $child->name }} <span class="text-xs text-slate-300">({{ $child->products_count }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <div>
        <h3 class="mb-2 font-semibold text-brand-800">Price ({{ settings('currency', 'AED') }})</h3>
        <div class="flex items-center gap-2">
            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ $priceBounds['min'] }}" class="field py-1.5">
            <span class="text-slate-400">–</span>
            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ $priceBounds['max'] }}" class="field py-1.5">
        </div>
    </div>

    <div>
        <h3 class="mb-2 font-semibold text-brand-800">Brand</h3>
        <ul class="max-h-48 space-y-1 overflow-y-auto pr-1">
            @foreach($filterBrands as $brand)
                <li>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="brands[]" value="{{ $brand->slug }}"
                               @checked(in_array($brand->slug, (array) request('brands', [])))
                               class="rounded border-slate-300 text-brand-500 focus:ring-brand-500">
                        <span>{{ $brand->name }}</span>
                    </label>
                </li>
            @endforeach
        </ul>
    </div>

    @foreach($filterAttributes as $attribute)
        <div>
            <h3 class="mb-2 font-semibold text-brand-800">{{ $attribute->name }}</h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach($attribute->values as $value)
                    @php $checked = in_array((string) $value->id, (array) request('attributes', [])); @endphp
                    <label class="cursor-pointer">
                        <input type="checkbox" name="attributes[]" value="{{ $value->id }}" @checked($checked) class="peer sr-only">
                        <span class="badge border {{ $checked ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-500' }}">
                            {{ $value->value }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach

    <div>
        <h3 class="mb-2 font-semibold text-brand-800">Rating</h3>
        @foreach([4 => '4 stars & up', 3 => '3 stars & up', 2 => '2 stars & up'] as $stars => $label)
            <label class="flex items-center gap-2 py-0.5">
                <input type="radio" name="rating" value="{{ $stars }}" @checked((int) request('rating') === $stars)
                       class="border-slate-300 text-brand-500 focus:ring-brand-500">
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>

    <div class="space-y-1.5">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="availability" value="in_stock" @checked(request('availability') === 'in_stock')
                   class="rounded border-slate-300 text-brand-500 focus:ring-brand-500">
            <span>In stock only</span>
        </label>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="on_sale" value="1" @checked(request()->boolean('on_sale'))
                   class="rounded border-slate-300 text-brand-500 focus:ring-brand-500">
            <span>On sale</span>
        </label>
    </div>

    <div class="flex gap-2">
        <button class="btn-primary flex-1 py-2">Apply</button>
        <a href="{{ url()->current() }}" class="btn-outline py-2">Reset</a>
    </div>
</form>
