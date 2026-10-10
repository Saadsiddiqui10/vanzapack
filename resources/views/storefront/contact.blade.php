<x-storefront-layout title="Contact Us" :breadcrumbs="[['label' => 'Contact']]">
    <div class="container-page py-10">
        <div class="grid gap-10 lg:grid-cols-2">
            <div>
                <h1 class="font-display text-2xl font-bold text-brand-800">Get in touch</h1>
                <p class="mt-2 text-slate-600">{{ $page?->content ? strip_tags($page->content) : 'Reach the VanzaPack team Monday to Saturday, 9:00AM – 6:00PM.' }}</p>

                <dl class="mt-6 space-y-3 text-sm">
                    @php($phone = settings('store_phone', config('store.phone')))
                    <div><dt class="font-semibold text-brand-800">Phone</dt><dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="text-slate-600 hover:text-brand-600">{{ $phone }}</a></dd></div>
                    <div><dt class="font-semibold text-brand-800">WhatsApp</dt><dd><button type="button" data-wa-href="{{ app(\App\Services\WhatsAppService::class)->supportLink() }}" class="text-slate-600 hover:text-brand-600">+{{ settings('whatsapp_country_code', '971') }} {{ settings('whatsapp_phone') }}</button></dd></div>
                    @foreach(['store_email' => 'General enquiries', 'store_sales_email' => 'Sales & quotes', 'store_support_email' => 'Customer support'] as $key => $label)
                        @if($email = settings($key, config('store.'.str_replace('store_', '', $key))))
                            <div><dt class="font-semibold text-brand-800">{{ $label }}</dt><dd><a href="mailto:{{ $email }}" class="text-slate-600 hover:text-brand-600">{{ $email }}</a></dd></div>
                        @endif
                    @endforeach
                    @if(($linkedin = settings('social_linkedin')) && trim((string) parse_url($linkedin, PHP_URL_PATH), '/') !== '')
                        <div><dt class="font-semibold text-brand-800">LinkedIn</dt><dd class="mt-1"><a href="{{ $linkedin }}" target="_blank" rel="noopener" aria-label="VanzaPack on LinkedIn" title="VanzaPack on LinkedIn"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-[#0A66C2] text-white transition hover:-translate-y-0.5 hover:shadow-lg">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/></svg>
                        </a></dd></div>
                    @endif
                    <div><dt class="font-semibold text-brand-800">Office</dt><dd class="text-slate-600">{{ settings('store_address', config('store.address')) }}</dd></div>
                    <div><dt class="font-semibold text-brand-800">Hours</dt><dd class="text-slate-600">{{ settings('business_hours', config('store.business_hours')) }}</dd></div>
                </dl>

                <button type="button" data-wa-href="{{ app(\App\Services\WhatsAppService::class)->supportLink() }}"
                   class="btn mt-6 bg-[#25D366] text-white hover:opacity-90">Chat on WhatsApp</button>
            </div>

            <form method="POST" action="{{ route('contact.store') }}" class="card space-y-4 p-6">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- Honeypot: hidden from people, bots fill it in --}}
                <div class="hidden" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
                <div><label class="label">Name</label><input name="name" value="{{ old('name') }}" required class="field"><x-input-error :messages="$errors->get('name')" class="mt-1" /></div>
                    <div><label class="label">Email</label><input name="email" type="email" value="{{ old('email') }}" required class="field"><x-input-error :messages="$errors->get('email')" class="mt-1" /></div>
                    <div><label class="label">Phone</label><input name="phone" value="{{ old('phone') }}" class="field"></div>
                    <div><label class="label">Subject</label><input name="subject" value="{{ old('subject') }}" class="field"></div>
                </div>
                <div><label class="label">Message</label><textarea name="message" rows="5" required class="field">{{ old('message') }}</textarea><x-input-error :messages="$errors->get('message')" class="mt-1" /></div>
                <button class="btn-primary">Send message</button>
            </form>
        </div>
    </div>
</x-storefront-layout>
