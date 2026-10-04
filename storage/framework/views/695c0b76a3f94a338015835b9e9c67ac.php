<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'metaDescription' => null,
    'ogImage' => null,
    'ogType' => 'website',
    'canonical' => null,
    'schema' => null,
    'breadcrumbs' => [],
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title' => null,
    'metaDescription' => null,
    'ogImage' => null,
    'ogType' => 'website',
    'canonical' => null,
    'schema' => null,
    'breadcrumbs' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $storeName = settings('store_name', config('store.name'));
    $pageTitle = $title ? $title.' — '.$storeName : settings('seo_default_title', $storeName);
    $desc = $metaDescription ?: settings('seo_default_description');
    $image = $ogImage ?: asset('images/og-default.png');
    $canonicalUrl = $canonical ?: url()->current();
    $ga = settings('google_analytics_id');
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e($pageTitle); ?></title>
    <meta name="description" content="<?php echo e($desc); ?>">
    <link rel="canonical" href="<?php echo e($canonicalUrl); ?>">
    <meta name="theme-color" content="#95c93f">

    <meta property="og:site_name" content="<?php echo e($storeName); ?>">
    <meta property="og:type" content="<?php echo e($ogType); ?>">
    <meta property="og:title" content="<?php echo e($pageTitle); ?>">
    <meta property="og:description" content="<?php echo e($desc); ?>">
    <meta property="og:url" content="<?php echo e($canonicalUrl); ?>">
    <meta property="og:image" content="<?php echo e($image); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo e($desc); ?>">
    <meta name="twitter:image" content="<?php echo e($image); ?>">

    <link rel="icon" href="<?php echo e(media(settings('favicon'), asset('favicon.ico'))); ?>">

    <script type="application/ld+json">
        <?php echo json_encode([
            '<?php $__contextArgs = [];
if (context()->has($__contextArgs[0])) :
if (isset($value)) { $__contextPrevious[] = $value; }
$value = context()->get($__contextArgs[0]); ?>' => 'https://schema.org',
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
        ], JSON_UNESCAPED_SLASHES); ?>

    </script>
    <?php if($schema): ?>
        <script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_SLASHES); ?></script>
    <?php endif; ?>
    <?php if(count($breadcrumbs)): ?>
        <script type="application/ld+json">
            <?php echo json_encode([
                '<?php $__contextArgs = [];
if (context()->has($__contextArgs[0])) :
if (isset($value)) { $__contextPrevious[] = $value; }
$value = context()->get($__contextArgs[0]); ?>' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($breadcrumbs)->values()->map(fn ($b, $i) => [
                    '@type' => 'ListItem', 'position' => $i + 1, 'name' => $b['label'],
                    'item' => $b['url'] ?? null,
                ])->all(),
            ], JSON_UNESCAPED_SLASHES); ?>

        </script>
    <?php endif; ?>

    <?php if($ga): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($ga); ?>"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo e($ga); ?>');</script>
    <?php endif; ?>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script>window.__gcCartCount = <?php echo e((int) ($cartCount ?? 0)); ?>;</script>
</head>
<body class="flex min-h-full flex-col bg-slate-50">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:m-3 focus:rounded focus:bg-brand-700 focus:px-4 focus:py-2 focus:text-white">Skip to content</a>

    <?php echo $__env->make('storefront.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main id="main" class="flex-1">
        <?php if(count($breadcrumbs)): ?>
            <nav class="container-page py-3 text-sm text-slate-500" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="<?php echo e(route('home')); ?>" class="hover:text-brand-600">Home</a></li>
                    <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li aria-hidden="true">/</li>
                        <li>
                            <?php if(!empty($crumb['url']) && !$loop->last): ?>
                                <a href="<?php echo e($crumb['url']); ?>" class="hover:text-brand-600"><?php echo e($crumb['label']); ?></a>
                            <?php else: ?>
                                <span class="text-brand-800"><?php echo e($crumb['label']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>
            </nav>
        <?php endif; ?>

        <?php echo e($slot); ?>

    </main>

    <?php echo $__env->make('storefront.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
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

    
    <div x-data="toastHub" @gc-toast.window="add($event.detail)"
         class="fixed bottom-4 right-4 z-[60] flex w-80 flex-col gap-2" x-cloak>
        <template x-for="toast in toasts" :key="toast.id">
            <div class="animate-fade-in-up rounded-lg px-4 py-3 text-sm text-white shadow-lg"
                 :class="toast.type === 'error' ? 'bg-rose-600' : 'bg-brand-700'">
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    
    <?php if(session('success') || session('error')): ?>
        <div x-data x-init="window.gcToast(<?php echo \Illuminate\Support\Js::from(session('success') ?? session('error'))->toHtml() ?>, '<?php echo e(session('success') ? 'success' : 'error'); ?>')"></div>
    <?php endif; ?>

    
    <a href="<?php echo e(app(\App\Services\WhatsAppService::class)->supportLink()); ?>" target="_blank" rel="noopener"
       class="fixed bottom-4 left-4 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition hover:scale-105"
       aria-label="Chat on WhatsApp">
        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.7-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.7 1-.9 1.1-.2.2-.3.2-.6.1-1.7-.8-2.8-1.5-3.9-3.4-.3-.5.3-.5.8-1.5.1-.2 0-.4 0-.5 0-.1-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.3 5.2 4.6 2.9 1.2 2.9.8 3.4.8.5 0 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.6-.3M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2"/></svg>
    </a>

    <button x-data="backToTop" x-show="show" x-cloak @click="up()"
            class="fixed bottom-4 right-4 z-40 hidden h-11 w-11 items-center justify-center rounded-full bg-brand-700 text-white shadow-lg sm:flex"
            style="bottom: 5.5rem" aria-label="Back to top">↑</button>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/components/storefront-layout.blade.php ENDPATH**/ ?>