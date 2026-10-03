<?php
    $announcementList = ($announcements ?? collect())->isNotEmpty()
        ? $announcements
        : collect(array_filter([settings('announcement_text')]))->map(fn ($t) => (object) ['text' => $t, 'url' => null]);
?>

<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 bg-white shadow-sm">
    <?php if($announcementList->isNotEmpty()): ?>
        <div class="bg-navy-600 text-white">
            <div class="marquee py-1.5 text-xs font-medium sm:text-sm" style="--marquee-duration: <?php echo e(max(18, $announcementList->count() * 12)); ?>s; --marquee-gap: 4rem;">
                <?php $__currentLoopData = [1, 2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="marquee__track" aria-hidden="<?php echo e($pass === 2 ? 'true' : 'false'); ?>">
                        <?php $__currentLoopData = $announcementList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="flex items-center gap-2 whitespace-nowrap">
                                🚚
                                <?php if($a->url): ?>
                                    <a href="<?php echo e($a->url); ?>" class="hover:underline"><?php echo e($a->text); ?></a>
                                <?php else: ?>
                                    <?php echo e($a->text); ?>

                                <?php endif; ?>
                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="border-b border-slate-100">
        <div class="container-page flex items-center gap-4 py-3">
            <button class="lg:hidden" @click="mobileOpen = true" aria-label="Open menu">
                <svg class="h-6 w-6 text-brand-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <a href="<?php echo e(route('home')); ?>" class="flex shrink-0 items-center">
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="<?php echo e(settings('store_name', 'VanzaPack')); ?>" class="h-10 w-auto sm:h-12" width="1476" height="352">
            </a>

            
            <div class="relative hidden flex-1 md:block" x-data="searchBox" @click.outside="open = false">
                <form action="<?php echo e(route('search')); ?>" method="GET" class="flex">
                    <input name="q" x-model="q" @input="onInput" @focus="q.length >= 2 && (open = true)"
                           autocomplete="off" placeholder="Search for products, brands, categories…"
                           class="field rounded-r-none border-r-0" aria-label="Search products">
                    <button class="btn-primary rounded-l-none px-4" aria-label="Search">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    </button>
                </form>

                <div x-show="open" x-cloak x-transition
                     class="absolute inset-x-0 top-full z-50 mt-1 overflow-hidden rounded-xl border border-slate-100 bg-white shadow-xl">
                    <template x-if="loading"><p class="p-4 text-sm text-slate-400">Searching…</p></template>
                    <template x-if="!loading && !hasResults"><p class="p-4 text-sm text-slate-400">No matches — try another term.</p></template>
                    <template x-if="!loading && results.categories.length">
                        <div class="border-b border-slate-100 p-2">
                            <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-400">Categories</p>
                            <template x-for="c in results.categories" :key="c.url">
                                <a :href="c.url" class="block rounded px-2 py-1.5 text-sm hover:bg-slate-50" x-text="c.name"></a>
                            </template>
                        </div>
                    </template>
                    <template x-if="!loading && results.products.length">
                        <div class="p-2">
                            <template x-for="p in results.products" :key="p.url">
                                <a :href="p.url" class="flex items-center gap-3 rounded px-2 py-1.5 hover:bg-slate-50">
                                    <img :src="p.image" alt="" class="h-10 w-10 rounded object-cover">
                                    <span class="flex-1 text-sm" x-text="p.name"></span>
                                    <span class="text-sm font-semibold text-brand-700" x-text="p.price"></span>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            
            <div class="flex items-center gap-1 sm:gap-3">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('account.dashboard')); ?>" class="btn-ghost hidden px-2 sm:inline-flex" title="My account">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn-ghost hidden px-2 sm:inline-flex" title="Sign in">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
                    </a>
                <?php endif; ?>

                <a href="<?php echo e(route('wishlist.index')); ?>" class="btn-ghost px-2" title="Wishlist">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 21s-7-4.35-9.5-8.5C1 9 3 5.5 6.5 5.5 8.7 5.5 10.5 7 12 9c1.5-2 3.3-3.5 5.5-3.5C21 5.5 23 9 21.5 12.5 19 16.65 12 21 12 21z"/></svg>
                </a>

                <button @click="$store.cart.openDrawer()" class="btn-ghost relative px-2" title="Cart">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l2.4 12.4A2 2 0 0 0 9.4 17h8.2a2 2 0 0 0 2-1.6L21 8H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
                    <span x-show="$store.cart.count > 0"
                          class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-500 px-1 text-xs font-bold text-white"
                          x-text="$store.cart.count"></span>
                </button>
            </div>
        </div>

        
        <div class="container-page pb-3 md:hidden">
            <form action="<?php echo e(route('search')); ?>" method="GET" class="flex">
                <input name="q" placeholder="Search products…" class="field rounded-r-none" aria-label="Search">
                <button class="btn-primary rounded-l-none px-4">Go</button>
            </form>
        </div>
    </div>

    
    <?php echo $__env->make('storefront.partials.mega-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" @keydown.escape.window="mobileOpen = false">
        <div class="absolute inset-0 bg-navy-900/40" @click="mobileOpen = false"></div>
        <div class="absolute left-0 top-0 h-full w-80 max-w-[85%] overflow-y-auto bg-white p-5"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
            <div class="mb-4 flex items-center justify-between">
                <span class="font-display text-lg font-bold text-brand-800">Menu</span>
                <button @click="mobileOpen = false" aria-label="Close">✕</button>
            </div>
            <nav class="space-y-1 text-sm">
                <?php $__empty_1 = true; $__currentLoopData = $navMenuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php if($item->isMega()): ?>
                        <p class="px-2 pb-1 pt-3 text-xs font-semibold uppercase tracking-wide text-slate-400"><?php echo e($item->label); ?></p>
                        <?php $__currentLoopData = $navCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div x-data="{ o: false }">
                                <button @click="o = !o" class="flex w-full items-center justify-between rounded px-2 py-2 text-left font-medium hover:bg-slate-50">
                                    <a href="<?php echo e(route('category.show', $cat->slug)); ?>" @click.stop><?php echo e($cat->name); ?></a>
                                    <?php if($cat->children->isNotEmpty()): ?><span x-text="o ? '−' : '+'" class="text-slate-400"></span><?php endif; ?>
                                </button>
                                <?php if($cat->children->isNotEmpty()): ?>
                                    <div x-show="o" x-cloak class="ml-3 border-l border-slate-100 pl-2">
                                        <?php $__currentLoopData = $cat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('category.show', $sub->slug)); ?>" class="block rounded px-2 py-1.5 text-slate-500 hover:bg-slate-50"><?php echo e($sub->name); ?></a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <a href="<?php echo e($item->href()); ?>" <?php if($item->open_in_new_tab): ?> target="_blank" rel="noopener" <?php endif; ?>
                           class="block rounded px-2 py-2 hover:bg-slate-50"><?php echo e($item->label); ?></a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <a href="<?php echo e(route('home')); ?>" class="block rounded px-2 py-2 hover:bg-slate-50">Home</a>
                    <a href="<?php echo e(route('shop.index')); ?>" class="block rounded px-2 py-2 hover:bg-slate-50">Shop</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/storefront/partials/header.blade.php ENDPATH**/ ?>