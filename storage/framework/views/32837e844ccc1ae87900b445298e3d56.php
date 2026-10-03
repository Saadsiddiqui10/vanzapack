<?php
    $featuredDeal = \App\Models\Product::active()->whereNotNull('sale_price')
        ->whereColumn('sale_price', '<', 'price')
        ->with('images')->inRandomOrder()->first();
?>

<nav class="hidden bg-navy-600 text-white lg:block" aria-label="Primary">
    <div class="container-page">
        <ul class="flex items-center gap-1 text-sm font-medium text-white">
            <?php $__empty_1 = true; $__currentLoopData = $navMenuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php if($menuItem->isMega()): ?>
                    <li x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="static">
                        <button class="flex items-center gap-1 px-3 py-3 transition hover:text-brand-300" @click="open = !open">
                            <?php echo e($menuItem->label); ?>

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute inset-x-0 top-full z-40 max-h-[75vh] overflow-y-auto border-t border-slate-100 bg-white shadow-xl">
                            <div class="container-page grid grid-cols-12 gap-8 py-8">
                                <div class="col-span-9 columns-2 gap-8 xl:columns-3 [column-fill:balance]">
                                    <?php $__currentLoopData = $navCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="mb-6 break-inside-avoid">
                                            <a href="<?php echo e(route('category.show', $parent->slug)); ?>"
                                               class="mb-2 block font-display text-sm font-bold uppercase tracking-wide text-brand-800 hover:text-brand-600">
                                                <?php echo e($parent->name); ?>

                                            </a>
                                            <ul class="space-y-1">
                                                <?php $__currentLoopData = $parent->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <a href="<?php echo e(route('category.show', $section->slug)); ?>"
                                                           class="text-sm <?php echo e($section->children->isNotEmpty() ? 'font-semibold text-slate-700' : 'text-slate-500'); ?> hover:text-brand-600">
                                                            <?php echo e($section->name); ?>

                                                        </a>
                                                        <?php if($section->children->isNotEmpty()): ?>
                                                            <ul class="mb-1 ml-3 mt-0.5 space-y-0.5 border-l border-slate-100 pl-3">
                                                                <?php $__currentLoopData = $section->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leaf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <li>
                                                                        <a href="<?php echo e(route('category.show', $leaf->slug)); ?>"
                                                                           class="text-xs text-slate-500 hover:text-brand-600"><?php echo e($leaf->name); ?></a>
                                                                    </li>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                <div class="col-span-3">
                                    <?php if($featuredDeal): ?>
                                        <a href="<?php echo e($featuredDeal->url()); ?>" class="group block overflow-hidden rounded-xl bg-slate-50">
                                            <img src="<?php echo e($featuredDeal->primaryImageUrl()); ?>" alt="<?php echo e($featuredDeal->name); ?>"
                                                 class="aspect-video w-full object-cover transition group-hover:scale-105">
                                            <div class="p-4">
                                                <span class="badge bg-brand-100 text-brand-800">Deal of the day</span>
                                                <p class="mt-1 line-clamp-2 text-sm font-semibold text-brand-800"><?php echo e($featuredDeal->name); ?></p>
                                                <p class="mt-1 text-sm text-brand-700"><?php echo e(money($featuredDeal->currentPrice())); ?>

                                                    <span class="text-slate-400 line-through"><?php echo e(money($featuredDeal->price)); ?></span>
                                                </p>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="<?php echo e($menuItem->href()); ?>" <?php if($menuItem->open_in_new_tab): ?> target="_blank" rel="noopener" <?php endif; ?>
                           class="block px-3 py-3 transition hover:text-brand-300"><?php echo e($menuItem->label); ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li><a href="<?php echo e(route('home')); ?>" class="block px-3 py-3 transition hover:text-brand-300">Home</a></li>
                <li><a href="<?php echo e(route('shop.index')); ?>" class="block px-3 py-3 transition hover:text-brand-300">Shop</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/storefront/partials/mega-menu.blade.php ENDPATH**/ ?>