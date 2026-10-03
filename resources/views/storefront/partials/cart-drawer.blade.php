@if(!$cart || $cart->items->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <p class="text-slate-400">Your cart is empty.</p>
        <a href="{{ route('shop.index') }}" class="btn-primary mt-4" @click="$store.cart.close()">Start shopping</a>
    </div>
@else
    <ul class="divide-y divide-slate-100">
        @foreach($cart->items as $item)
            <li class="flex gap-3 py-4" x-data="cartLine({{ $item->id }})">
                <img src="{{ $item->variant?->imageUrl() ?? $item->product->primaryImageUrl() }}"
                     alt="{{ $item->product->name }}" class="h-16 w-16 rounded-lg object-cover">
                <div class="flex-1">
                    <a href="{{ $item->product->url() }}" class="line-clamp-2 text-sm font-medium text-brand-800">{{ $item->product->name }}</a>
                    @if($item->variant)
                        <p class="text-xs text-slate-400">{{ $item->variant->label() }}</p>
                    @endif
                    <div class="mt-1.5 flex items-center gap-2">
                        <div class="flex items-center rounded border border-slate-200">
                            <button class="px-2 text-slate-500" @click="update({{ $item->quantity - 1 }})" :disabled="busy">−</button>
                            <span class="w-8 text-center text-sm">{{ $item->quantity }}</span>
                            <button class="px-2 text-slate-500" @click="update({{ $item->quantity + 1 }})" :disabled="busy">+</button>
                        </div>
                        <button class="text-xs text-rose-500 hover:underline" @click="remove()" :disabled="busy">Remove</button>
                    </div>
                </div>
                <div class="text-sm font-semibold text-brand-800">{{ money($item->lineTotal()) }}</div>
            </li>
        @endforeach
    </ul>

    <div class="mt-4 space-y-1.5 border-t border-slate-100 pt-4 text-sm">
        <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span class="font-medium">{{ money($totals->subtotal) }}</span></div>
        @if($totals->discount > 0)
            <div class="flex justify-between text-brand-700"><span>Discount ({{ $totals->couponCode }})</span><span>−{{ money($totals->discount) }}</span></div>
        @endif
        <p class="text-xs text-slate-400">Shipping &amp; tax calculated at checkout.</p>
    </div>

    <div class="mt-4 space-y-2">
        <a href="{{ route('checkout.index') }}" class="btn-primary w-full">Checkout</a>
        <a href="{{ route('cart.index') }}" class="btn-outline w-full" @click="$store.cart.close()">View cart</a>
        @if(app(\App\Services\WhatsAppService::class)->enabledForCart())
            <a href="{{ app(\App\Services\WhatsAppService::class)->cartLink($cart) }}" target="_blank" rel="noopener"
               class="btn w-full bg-[#25D366] text-white hover:opacity-90">Order via WhatsApp</a>
        @endif
    </div>
@endif
