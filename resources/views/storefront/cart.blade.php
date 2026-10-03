<x-storefront-layout title="Your Cart" :breadcrumbs="[['label' => 'Cart']]">
    <div class="container-page py-8">
        <h1 class="mb-6 font-display text-2xl font-bold text-brand-800">Your Cart</h1>

        @if(!$cart || $cart->items->isEmpty())
            <div class="card p-12 text-center">
                <p class="text-slate-500">Your cart is empty.</p>
                <a href="{{ route('shop.index') }}" class="btn-primary mt-4">Browse products</a>
            </div>
        @else
            <div class="lg:grid lg:grid-cols-[1fr_22rem] lg:gap-8">
                <div class="card divide-y divide-slate-100">
                    @foreach($cart->items as $item)
                        <div class="flex gap-4 p-4" x-data="cartLine({{ $item->id }})">
                            <img src="{{ $item->variant?->imageUrl() ?? $item->product->primaryImageUrl() }}"
                                 alt="{{ $item->product->name }}" class="h-24 w-24 rounded-lg object-cover">
                            <div class="flex flex-1 flex-col">
                                <a href="{{ $item->product->url() }}" class="font-medium text-brand-800 hover:text-brand-600">{{ $item->product->name }}</a>
                                @if($item->variant)<p class="text-sm text-slate-400">{{ $item->variant->label() }}</p>@endif
                                <p class="text-sm text-slate-400">{{ money($item->unit_price) }} each</p>
                                <div class="mt-auto flex items-center gap-3 pt-2">
                                    <div class="flex items-center rounded-lg border border-slate-200">
                                        <button class="px-2.5 py-1 text-slate-500" @click="update({{ $item->quantity - 1 }})" :disabled="busy">−</button>
                                        <span class="w-10 text-center text-sm">{{ $item->quantity }}</span>
                                        <button class="px-2.5 py-1 text-slate-500" @click="update({{ $item->quantity + 1 }})" :disabled="busy">+</button>
                                    </div>
                                    <button class="text-sm text-rose-500 hover:underline" @click="remove()" :disabled="busy">Remove</button>
                                </div>
                            </div>
                            <p class="font-semibold text-brand-800">{{ money($item->lineTotal()) }}</p>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between p-4">
                        <a href="{{ route('shop.index') }}" class="link text-sm">← Continue shopping</a>
                        <form method="POST" action="{{ route('cart.clear') }}">
                            @csrf @method('DELETE')
                            <button class="text-sm text-slate-400 hover:text-rose-500">Clear cart</button>
                        </form>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="mt-6 lg:mt-0">
                    <div class="card p-5">
                        <h2 class="font-semibold text-brand-800">Order Summary</h2>

                        <form method="POST" action="{{ route('cart.coupon.apply') }}" class="mt-4 flex gap-2">
                            @csrf
                            <input name="code" value="{{ $cart->coupon_code }}" placeholder="Coupon code" class="field py-2 text-sm">
                            <button class="btn-outline py-2 text-sm">Apply</button>
                        </form>
                        @if($totals->couponCode)
                            <form method="POST" action="{{ route('cart.coupon.remove') }}" class="mt-1">
                                @csrf @method('DELETE')
                                <button class="text-xs text-rose-500 hover:underline">Remove “{{ $totals->couponCode }}”</button>
                            </form>
                        @endif

                        <dl class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ money($totals->subtotal) }}</dd></div>
                            @if($totals->discount > 0)
                                <div class="flex justify-between text-brand-700"><dt>Discount</dt><dd>−{{ money($totals->discount) }}</dd></div>
                            @endif
                            <div class="flex justify-between text-slate-400"><dt>Shipping</dt><dd>Calculated at checkout</dd></div>
                            <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-semibold text-brand-800">
                                <dt>Estimated total</dt><dd>{{ money($totals->subtotal - $totals->discount) }}</dd>
                            </div>
                        </dl>

                        <a href="{{ route('checkout.index') }}" class="btn-primary mt-4 w-full">Proceed to Checkout</a>

                        @if(app(\App\Services\WhatsAppService::class)->enabledForCart())
                            <a href="{{ app(\App\Services\WhatsAppService::class)->cartLink($cart) }}" target="_blank" rel="noopener"
                               class="btn mt-2 w-full bg-[#25D366] text-white hover:opacity-90">Order via WhatsApp</a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-storefront-layout>
