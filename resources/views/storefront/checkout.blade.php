@php $u = auth()->user(); $default = $addresses->firstWhere('is_default_shipping', true) ?? $addresses->first(); @endphp

<x-storefront-layout title="Checkout" :breadcrumbs="[['label' => 'Cart', 'url' => route('cart.index')], ['label' => 'Checkout']]">
    <div class="container-page py-8">
        <h1 class="mb-6 font-display text-2xl font-bold text-brand-800">Checkout</h1>

        <form method="POST" action="{{ route('checkout.store') }}"
              x-data="{ billingSame: true, method: '{{ old('shipping_method_id', $selectedMethodId) }}', payment: '{{ old('payment_method', array_key_first($gateways)) }}' }"
              class="lg:grid lg:grid-cols-[1fr_22rem] lg:gap-8">
            @csrf

            <div class="space-y-6">
                {{-- 1. Contact --}}
                <section class="card p-5">
                    <h2 class="mb-4 font-semibold text-brand-800">1. Contact information</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">Email</label>
                            <input name="email" type="email" value="{{ old('email', $u?->email) }}" required class="field">
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                        <div>
                            <label class="label">Phone</label>
                            <input name="phone" value="{{ old('phone', $u?->phone) }}" required class="field">
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>
                    </div>
                    @guest
                        <p class="mt-3 text-sm text-slate-500">Have an account? <a href="{{ route('login') }}" class="link">Sign in</a> for faster checkout.</p>
                    @endguest
                </section>

                {{-- 2. Shipping address --}}
                <section class="card p-5">
                    <h2 class="mb-4 font-semibold text-brand-800">2. Shipping address</h2>

                    @if($addresses->isNotEmpty())
                        <div class="mb-4 grid gap-2 sm:grid-cols-2" x-data>
                            @foreach($addresses as $address)
                                <label class="cursor-pointer rounded-lg border border-slate-200 p-3 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                                    <input type="radio" name="_saved_address" class="sr-only" @checked($address->id === $default?->id)
                                           @change="
                                               $refs.f_first.value=@js($address->first_name);
                                               $refs.f_last.value=@js($address->last_name);
                                               $refs.f_phone.value=@js($address->phone);
                                               $refs.f_line1.value=@js($address->line1);
                                               $refs.f_line2.value=@js($address->line2);
                                               $refs.f_city.value=@js($address->city);
                                               $refs.f_state.value=@js($address->state);
                                               $refs.f_postal.value=@js($address->postal_code);
                                               $refs.f_country.value=@js($address->country);
                                           ">
                                    <span class="font-medium text-brand-800">{{ $address->label }}</span><br>
                                    {{ $address->fullName() }}, {{ $address->line1 }}, {{ $address->city }}
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="label">First name</label><input x-ref="f_first" name="ship[first_name]" value="{{ old('ship.first_name', $default?->first_name ?? $u?->name) }}" required class="field"></div>
                        <div><label class="label">Last name</label><input x-ref="f_last" name="ship[last_name]" value="{{ old('ship.last_name', $default?->last_name) }}" required class="field"></div>
                        <div><label class="label">Phone</label><input x-ref="f_phone" name="ship[phone]" value="{{ old('ship.phone', $default?->phone ?? $u?->phone) }}" required class="field"></div>
                        <div><label class="label">Country</label>
                            <select x-ref="f_country" name="ship[country]" class="field">
                                @foreach(['AE' => 'United Arab Emirates', 'SA' => 'Saudi Arabia', 'QA' => 'Qatar', 'OM' => 'Oman', 'KW' => 'Kuwait', 'BH' => 'Bahrain'] as $code => $name)
                                    <option value="{{ $code }}" @selected(old('ship.country', $default?->country ?? 'AE') === $code)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2"><label class="label">Address line 1</label><input x-ref="f_line1" name="ship[line1]" value="{{ old('ship.line1', $default?->line1) }}" required class="field"></div>
                        <div class="sm:col-span-2"><label class="label">Address line 2 <span class="text-slate-400">(optional)</span></label><input x-ref="f_line2" name="ship[line2]" value="{{ old('ship.line2', $default?->line2) }}" class="field"></div>
                        <div><label class="label">City</label><input x-ref="f_city" name="ship[city]" value="{{ old('ship.city', $default?->city) }}" required class="field"></div>
                        <div><label class="label">State / Emirate</label><input x-ref="f_state" name="ship[state]" value="{{ old('ship.state', $default?->state) }}" class="field"></div>
                        <div><label class="label">Postal code</label><input x-ref="f_postal" name="ship[postal_code]" value="{{ old('ship.postal_code', $default?->postal_code) }}" class="field"></div>
                    </div>
                    <x-input-error :messages="$errors->get('ship.line1')" class="mt-2" />

                    @auth
                        <label class="mt-3 flex items-center gap-2 text-sm">
                            <input type="checkbox" name="save_address" value="1" class="rounded border-slate-300 text-brand-500">
                            Save this address to my account
                        </label>
                    @endauth
                </section>

                {{-- Billing --}}
                <section class="card p-5">
                    <label class="flex items-center gap-2 text-sm font-medium text-brand-800">
                        <input type="checkbox" x-model="billingSame" class="rounded border-slate-300 text-brand-500">
                        Billing address same as shipping
                    </label>
                    <input type="hidden" name="billing_same" :value="billingSame ? 1 : 0">
                    <div x-show="!billingSame" x-cloak class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div><label class="label">First name</label><input name="bill[first_name]" value="{{ old('bill.first_name') }}" class="field"></div>
                        <div><label class="label">Last name</label><input name="bill[last_name]" value="{{ old('bill.last_name') }}" class="field"></div>
                        <div><label class="label">Phone</label><input name="bill[phone]" value="{{ old('bill.phone') }}" class="field"></div>
                        <div><label class="label">Country</label>
                            <select name="bill[country]" class="field">
                                @foreach(['AE' => 'United Arab Emirates', 'SA' => 'Saudi Arabia', 'QA' => 'Qatar', 'OM' => 'Oman', 'KW' => 'Kuwait', 'BH' => 'Bahrain'] as $code => $name)
                                    <option value="{{ $code }}" @selected(old('bill.country') === $code)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2"><label class="label">Address line 1</label><input name="bill[line1]" value="{{ old('bill.line1') }}" class="field"></div>
                        <div class="sm:col-span-2"><label class="label">Address line 2</label><input name="bill[line2]" value="{{ old('bill.line2') }}" class="field"></div>
                        <div><label class="label">City</label><input name="bill[city]" value="{{ old('bill.city') }}" class="field"></div>
                        <div><label class="label">State</label><input name="bill[state]" value="{{ old('bill.state') }}" class="field"></div>
                        <div><label class="label">Postal code</label><input name="bill[postal_code]" value="{{ old('bill.postal_code') }}" class="field"></div>
                    </div>
                </section>

                {{-- 3. Delivery method --}}
                <section class="card p-5">
                    <h2 class="mb-4 font-semibold text-brand-800">3. Delivery method</h2>
                    <div class="space-y-2">
                        @foreach($shippingMethods as $sm)
                            <label class="flex cursor-pointer items-center justify-between rounded-lg border border-slate-200 p-3 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                                <span class="flex items-center gap-3">
                                    <input type="radio" name="shipping_method_id" value="{{ $sm->id }}" x-model="method"
                                           class="border-slate-300 text-brand-500" @checked($sm->id === $selectedMethodId)>
                                    <span>
                                        <span class="font-medium text-brand-800">{{ $sm->name }}</span><br>
                                        <span class="text-slate-400">{{ $sm->estimated_delivery }}</span>
                                    </span>
                                </span>
                                <span class="font-semibold">{{ $sm->costFor($totals->subtotal) == 0 ? 'Free' : money($sm->costFor($totals->subtotal)) }}</span>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- 4. Payment --}}
                <section class="card p-5">
                    <h2 class="mb-4 font-semibold text-brand-800">4. Payment method</h2>
                    <div class="space-y-2">
                        @foreach($gateways as $key => $gateway)
                            <label class="block cursor-pointer rounded-lg border border-slate-200 p-3 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                                <span class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="{{ $key }}" x-model="payment"
                                           class="border-slate-300 text-brand-500" @checked($loop->first)>
                                    <span class="font-medium text-brand-800">{{ $gateway->label() }}</span>
                                </span>
                                @if($key === 'bank_transfer' && $bankDetails)
                                    <pre x-show="payment === 'bank_transfer'" class="mt-2 whitespace-pre-wrap rounded bg-slate-50 p-3 text-xs text-slate-600">{{ $bankDetails }}</pre>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                </section>

                {{-- 5. Notes --}}
                <section class="card p-5">
                    <h2 class="mb-3 font-semibold text-brand-800">5. Delivery notes <span class="text-sm font-normal text-slate-400">(optional)</span></h2>
                    <textarea name="customer_note" rows="3" class="field">{{ old('customer_note') }}</textarea>
                </section>
            </div>

            {{-- Order review --}}
            <div class="mt-6 lg:mt-0">
                <div class="card sticky top-24 p-5">
                    <h2 class="mb-4 font-semibold text-brand-800">Order review</h2>
                    <ul class="max-h-64 space-y-3 overflow-y-auto">
                        @foreach($cart->items as $item)
                            <li class="flex gap-3 text-sm">
                                <img src="{{ $item->variant?->imageUrl() ?? $item->product->primaryImageUrl() }}" alt="" class="h-12 w-12 rounded object-cover">
                                <div class="flex-1">
                                    <p class="line-clamp-1 font-medium text-brand-800">{{ $item->product->name }}</p>
                                    <p class="text-slate-400">Qty {{ $item->quantity }}{{ $item->variant ? ' · '.$item->variant->label() : '' }}</p>
                                </div>
                                <span>{{ money($item->lineTotal()) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <dl class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ money($totals->subtotal) }}</dd></div>
                        @if($totals->discount > 0)
                            <div class="flex justify-between text-brand-700"><dt>Discount ({{ $totals->couponCode }})</dt><dd>−{{ money($totals->discount) }}</dd></div>
                        @endif
                        <div class="flex justify-between"><dt class="text-slate-500">Shipping</dt><dd>{{ $totals->shipping == 0 ? 'Free' : money($totals->shipping) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">VAT</dt><dd>{{ money($totals->tax) }}</dd></div>
                        <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-brand-800">
                            <dt>Total</dt><dd>{{ money($totals->grandTotal()) }}</dd>
                        </div>
                    </dl>

                    <button class="btn-primary mt-4 w-full">Place Order</button>
                    <p class="mt-2 text-center text-xs text-slate-400">By placing your order you agree to our
                        <a href="{{ route('page.show', 'terms-conditions') }}" class="link">Terms</a>.</p>
                </div>
            </div>
        </form>
    </div>
</x-storefront-layout>
