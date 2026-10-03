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
                    <p>📞 {{ settings('store_phone', config('store.phone')) }}</p>
                    <p>✉️ {{ settings('store_email', config('store.email')) }}</p>
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

        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 text-xs text-slate-400 sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ settings('store_name', 'VanzaPack') }}. All rights reserved.</p>
            <div class="flex gap-4">
                <a href="{{ settings('social_facebook', '#') }}" class="hover:text-white">Facebook</a>
                <a href="{{ settings('social_instagram', '#') }}" class="hover:text-white">Instagram</a>
                <a href="{{ settings('social_linkedin', '#') }}" class="hover:text-white">LinkedIn</a>
                <a href="{{ settings('social_tiktok', '#') }}" class="hover:text-white">TikTok</a>
            </div>
        </div>
    </div>
</footer>
