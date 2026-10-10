<footer class="mt-16 bg-navy-600 text-slate-200">
    <div class="container-page py-12">
        {{-- Newsletter --}}
        <div class="mb-10 rounded-2xl bg-brand-800/60 p-6 sm:flex sm:items-center sm:justify-between sm:p-8">
            <div class="mb-4 sm:mb-0 sm:mr-8">
                <h3 class="font-display text-xl font-bold text-white">Stay updated with {{ settings('store_name', 'VanzaPack') }}</h3>
                <p class="mt-1 text-sm text-slate-300">New products, special offers and packaging tips in your inbox.</p>
            </div>
            <form action="{{ route('newsletter.store') }}" method="POST" x-data="asyncForm" @submit.prevent="submit"
                  class="flex w-full max-w-md gap-2">
                @csrf
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                <input type="email" name="email" required placeholder="Enter your email address"
                       class="field border-0 text-slate-800" aria-label="Email address">
                <button class="btn-primary shrink-0" :disabled="busy" x-text="busy ? 'Joining…' : 'Join'"></button>
            </form>
        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <span class="inline-block rounded-lg bg-white px-3 py-2">
                    <img src="{{ asset('images/logo.png') }}" alt="VanzaPack" class="h-10 w-auto" loading="lazy">
                </span>
                <p class="mt-3 max-w-sm text-sm text-slate-300">
                    Your trusted source for food packaging, cleaning products and everyday business supplies across the UAE.
                </p>
                <div class="mt-4 space-y-1 text-sm text-slate-300">
                    <p>💬 WhatsApp: <button type="button" data-wa-href="{{ app(\App\Services\WhatsAppService::class)->supportLink() }}" class="hover:text-white">+{{ settings('whatsapp_country_code', '971') }} {{ settings('whatsapp_phone') }}</button></p>
                    @foreach(['store_email' => 'Info', 'store_sales_email' => 'Sales', 'store_support_email' => 'Support'] as $key => $label)
                        @if($email = settings($key, config('store.'.str_replace('store_', '', $key))))
                            <p>✉️ {{ $label }}: <a href="mailto:{{ $email }}" class="hover:text-white">{{ $email }}</a></p>
                        @endif
                    @endforeach
                    <p>📍 {{ settings('store_address', config('store.address')) }}</p>
                </div>
            </div>

            <div>
                <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-white">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('shop.index') }}" class="hover:text-brand-400">All Products</a></li>
                    <li><a href="{{ route('shop.offers') }}" class="hover:text-brand-400">Mega Deals</a></li>
                    <li><a href="{{ route('shop.new') }}" class="hover:text-brand-400">New Arrivals</a></li>
                    <li><a href="{{ route('brands.index') }}" class="hover:text-brand-400">Brands</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-white">Information</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('page.show', 'privacy-policy') }}" class="hover:text-brand-400">Privacy Policy</a></li>
                    <li><a href="{{ route('page.show', 'terms-conditions') }}" class="hover:text-brand-400">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('page.show', 'return-policy') }}" class="hover:text-brand-400">Return Policy</a></li>
                    <li><a href="{{ route('page.show', 'shipping-delivery') }}" class="hover:text-brand-400">Shipping &amp; Delivery</a></li>
                    <li><a href="{{ route('page.show', 'faqs') }}" class="hover:text-brand-400">FAQs</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-white">Support</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('contact.show') }}" class="hover:text-brand-400">Contact Us</a></li>
                    <li><a href="{{ route('account.dashboard') }}" class="hover:text-brand-400">My Account</a></li>
                    <li><a href="{{ route('account.orders') }}" class="hover:text-brand-400">Track Orders</a></li>
                    <li><a href="{{ route('wishlist.index') }}" class="hover:text-brand-400">Wishlist</a></li>
                </ul>
            </div>
        </div>

        {{-- extra bottom padding keeps the floating WhatsApp button from covering this row --}}
        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-white/10 pb-16 pt-6 text-xs text-slate-400 sm:flex-row sm:pb-20">
            <p>&copy; {{ date('Y') }} {{ settings('store_name', 'VanzaPack') }}. All rights reserved.</p>
            <div class="flex items-center gap-3">
                {{-- Only real profile URLs (skips "#" and bare homepages like https://facebook.com) --}}
                @foreach([
                    'social_linkedin' => ['LinkedIn', 'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z'],
                    'social_facebook' => ['Facebook', 'M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.68.23 2.68.23v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.26h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07z'],
                    'social_instagram' => ['Instagram', 'M12 2.16c3.2 0 3.58.01 4.85.07 3.25.15 4.77 1.69 4.92 4.92.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.15 3.23-1.66 4.77-4.92 4.92-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-3.26-.15-4.77-1.7-4.92-4.92C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85C2.38 3.92 3.9 2.38 7.15 2.23 8.42 2.17 8.8 2.16 12 2.16zM12 0C8.74 0 8.33.01 7.05.07 2.7.27.27 2.69.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.2 4.36 2.62 6.78 6.98 6.98C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c4.35-.2 6.78-2.62 6.98-6.98.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.2-4.35-2.62-6.78-6.98-6.98C15.67.01 15.26 0 12 0zm0 5.84a6.16 6.16 0 1 0 0 12.32 6.16 6.16 0 0 0 0-12.32zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.85a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88z'],
                    'social_tiktok' => ['TikTok', 'M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z'],
                ] as $key => [$label, $icon])
                    @php($url = settings($key))
                    @if($url && trim((string) parse_url($url, PHP_URL_PATH), '/') !== '')
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $label }}" title="{{ $label }}"
                           class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:-translate-y-0.5 hover:bg-[#0A66C2]">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icon }}"/></svg>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</footer>
