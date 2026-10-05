<x-storefront-layout title="Contact Us" :breadcrumbs="[['label' => 'Contact']]">
    <div class="container-page py-10">
        <div class="grid gap-10 lg:grid-cols-2">
            <div>
                <h1 class="font-display text-2xl font-bold text-brand-800">Get in touch</h1>
                <p class="mt-2 text-slate-600">{{ $page?->content ? strip_tags($page->content) : 'Reach the VanzaPack team Monday to Saturday, 9:00AM – 6:00PM.' }}</p>

                <dl class="mt-6 space-y-3 text-sm">
                    @php($phone = settings('store_phone', config('store.phone')))
                    <div><dt class="font-semibold text-brand-800">Phone</dt><dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="text-slate-600 hover:text-brand-600">{{ $phone }}</a></dd></div>
                    <div><dt class="font-semibold text-brand-800">WhatsApp</dt><dd><a href="{{ app(\App\Services\WhatsAppService::class)->supportLink() }}" target="_blank" rel="noopener" class="text-slate-600 hover:text-brand-600">+{{ settings('whatsapp_country_code', '971') }} {{ settings('whatsapp_phone') }}</a></dd></div>
                    @foreach(['store_email' => 'General enquiries', 'store_sales_email' => 'Sales & quotes', 'store_support_email' => 'Customer support'] as $key => $label)
                        @if($email = settings($key, config('store.'.str_replace('store_', '', $key))))
                            <div><dt class="font-semibold text-brand-800">{{ $label }}</dt><dd><a href="mailto:{{ $email }}" class="text-slate-600 hover:text-brand-600">{{ $email }}</a></dd></div>
                        @endif
                    @endforeach
                    @if(($linkedin = settings('social_linkedin')) && trim((string) parse_url($linkedin, PHP_URL_PATH), '/') !== '')
                        <div><dt class="font-semibold text-brand-800">LinkedIn</dt><dd><a href="{{ $linkedin }}" target="_blank" rel="noopener" class="text-slate-600 hover:text-brand-600">Follow VanzaPack on LinkedIn</a></dd></div>
                    @endif
                    <div><dt class="font-semibold text-brand-800">Office</dt><dd class="text-slate-600">{{ settings('store_address', config('store.address')) }}</dd></div>
                    <div><dt class="font-semibold text-brand-800">Hours</dt><dd class="text-slate-600">{{ settings('business_hours', config('store.business_hours')) }}</dd></div>
                </dl>

                <a href="{{ app(\App\Services\WhatsAppService::class)->supportLink() }}" target="_blank" rel="noopener"
                   class="btn mt-6 bg-[#25D366] text-white hover:opacity-90">Chat on WhatsApp</a>
            </div>

            <form method="POST" action="{{ route('contact.store') }}" class="card space-y-4 p-6">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
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
