@props([
    'title' => null,
    'metaDescription' => null,
    'ogImage' => null,
    'ogType' => 'website',
    'canonical' => null,
    'schema' => null,
    'breadcrumbs' => [],
])

@php
    $storeName = settings('store_name', config('store.name'));
    // Brand first: "VanzaPack | Shop All Products". SEO titles that already end with the
    // store name (e.g. "Kraft Cups | VanzaPack UAE") have that suffix removed to avoid repeating it.
    $cleanTitle = $title ? trim(preg_replace('/\s*[|—–-]\s*'.preg_quote($storeName, '/').'(\s+UAE)?\s*$/iu', '', $title)) : '';
    $pageTitle = $cleanTitle !== '' && mb_strtolower($cleanTitle) !== mb_strtolower($storeName)
        ? $storeName.' | '.$cleanTitle
        : settings('seo_default_title', $storeName);
    $desc = $metaDescription ?: settings('seo_default_description');
    $image = $ogImage ?: asset('images/og-default.png');
    $canonicalUrl = $canonical ?: url()->current();
    $ga = settings('google_analytics_id');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $desc }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta name="theme-color" content="#95c93f">

    <meta property="og:site_name" content="{{ $storeName }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $image }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $desc }}">
    <meta name="twitter:image" content="{{ $image }}">

    @include('partials.favicons')

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $storeName,
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'email' => settings('store_email', config('store.email')),
            'sameAs' => array_values(array_filter([settings('social_linkedin')])),
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => settings('store_phone', config('store.phone')),
                    'email' => settings('store_support_email', config('store.support_email')),
                    'contactType' => 'customer service',
                ],
                [
                    '@type' => 'ContactPoint',
                    'email' => settings('store_sales_email', config('store.sales_email')),
                    'contactType' => 'sales',
                ],
            ],
        ], JSON_UNESCAPED_SLASHES) !!}
    </script>
    @if($schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES) !!}</script>
    @endif
    @if(count($breadcrumbs))
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($breadcrumbs)->values()->map(fn ($b, $i) => [
                    '@type' => 'ListItem', 'position' => $i + 1, 'name' => $b['label'],
                    'item' => $b['url'] ?? null,
                ])->all(),
            ], JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif

    @if($ga)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $ga }}');</script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>window.__gcCartCount = {{ (int) ($cartCount ?? 0) }};</script>
</head>
<body class="flex min-h-full flex-col bg-slate-50">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:m-3 focus:rounded focus:bg-brand-700 focus:px-4 focus:py-2 focus:text-white">Skip to content</a>

    @include('storefront.partials.header')

    <main id="main" class="flex-1">
        @if(count($breadcrumbs))
            <nav class="container-page py-3 text-sm text-slate-500" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-600">Home</a></li>
                    @foreach($breadcrumbs as $crumb)
                        <li aria-hidden="true">/</li>
                        <li>
                            @if(!empty($crumb['url']) && !$loop->last)
                                <a href="{{ $crumb['url'] }}" class="hover:text-brand-600">{{ $crumb['label'] }}</a>
                            @else
                                <span class="text-brand-800">{{ $crumb['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        {{ $slot }}
    </main>

    @include('storefront.partials.footer')

    {{-- Cart drawer --}}
    <div x-data x-cloak
         x-show="$store.cart.open"
         class="fixed inset-0 z-50"
         @keydown.escape.window="$store.cart.close()">
        <div class="absolute inset-0 bg-navy-900/40" @click="$store.cart.close()" x-transition.opacity></div>
        <div class="absolute right-0 top-0 flex h-full w-full max-w-md flex-col bg-white shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            <div class="flex items-center justify-between border-b px-5 py-4">
                <h2 class="text-lg font-semibold">Your Cart</h2>
                <button @click="$store.cart.close()" aria-label="Close cart" class="rounded p-1 hover:bg-slate-100">✕</button>
            </div>
            <div class="flex-1 overflow-y-auto p-5" x-html="$store.cart.drawerHtml">
                <template x-if="$store.cart.loading"><p class="text-center text-sm text-slate-400">Loading…</p></template>
            </div>
        </div>
    </div>

    {{-- Quick view modal --}}
    <div x-data="quickView" x-cloak
         @quick-view.window="show($event.detail)"
         x-show="open"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="open = false">
        <div class="absolute inset-0 bg-navy-900/50" @click="open = false" x-transition.opacity></div>
        <div class="relative z-10 max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl"
             x-transition>
            <button @click="open = false" class="absolute right-4 top-4 rounded p-1 hover:bg-slate-100" aria-label="Close">✕</button>
            <template x-if="loading"><p class="py-16 text-center text-sm text-slate-400">Loading…</p></template>
            <div x-html="html"></div>
        </div>
    </div>

    {{-- Toasts --}}
    <div x-data="toastHub" @vp-toast.window="add($event.detail)"
         class="fixed bottom-4 right-4 z-[60] flex w-80 flex-col gap-2" x-cloak>
        <template x-for="toast in toasts" :key="toast.id">
            <div class="animate-fade-in-up rounded-lg px-4 py-3 text-sm text-white shadow-lg"
                 :class="toast.type === 'error' ? 'bg-rose-600' : 'bg-brand-700'">
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    {{-- Session flash → toast --}}
    @if(session('success') || session('error'))
        <div x-data x-init="window.vpToast(@js(session('success') ?? session('error')), '{{ session('success') ? 'success' : 'error' }}')"></div>
    @endif

    {{-- WhatsApp float + back to top --}}
    {{-- A button (not a link) so the browser doesn't show the wa.me URL on hover --}}
    <button type="button" data-wa-href="{{ app(\App\Services\WhatsAppService::class)->supportLink() }}"
            class="wa-float fixed bottom-3 left-3 z-40 block sm:bottom-4 sm:left-4"
            aria-label="Click for WhatsApp Chat" title="Chat with us on WhatsApp">
        <img src="{{ asset('images/whatsapp-button.png') }}" alt="Click for WhatsApp Chat"
             width="640" height="146" class="h-12 w-auto sm:h-14" draggable="false">
    </button>

    <button x-data="backToTop" x-show="show" x-cloak @click="up()"
            class="fixed bottom-4 right-4 z-40 hidden h-11 w-11 items-center justify-center rounded-full bg-brand-700 text-white shadow-lg sm:flex"
            style="bottom: 5.5rem" aria-label="Back to top">↑</button>

    @stack('scripts')
</body>
</html>
