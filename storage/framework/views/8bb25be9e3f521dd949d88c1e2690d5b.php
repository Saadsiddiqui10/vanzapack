<?php if (isset($component)) { $__componentOriginal15d9730126555fea898e8a62f8938736 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal15d9730126555fea898e8a62f8938736 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    
    <section x-data="{ i: 0, count: <?php echo e(max(1, $heroBanners->count())); ?> }"
             x-init="count > 1 && setInterval(() => i = (i + 1) % count, 6000)"
             class="relative w-full overflow-hidden bg-gradient-to-br from-brand-50 to-brand-100
                    h-[62vw] max-h-[560px] min-h-[340px] sm:h-[46vw] lg:h-[38vw]">

        <?php $__empty_1 = true; $__currentLoopData = $heroBanners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div x-show="i === <?php echo e($index); ?>"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-500 absolute"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute inset-0">
                
                <img src="<?php echo e($banner->imageUrl()); ?>" alt="<?php echo e($banner->title); ?>"
                     class="absolute inset-0 h-full w-full object-cover">
                
                <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-white/10 md:to-transparent"></div>

                <div class="container-page relative flex h-full flex-col justify-center">
                    <div class="max-w-xl">
                        <h1 class="font-display text-2xl font-extrabold leading-tight text-brand-800 sm:text-3xl lg:text-5xl"><?php echo e($banner->title); ?></h1>
                        <p class="mt-3 max-w-md text-sm text-slate-600 sm:text-base"><?php echo e($banner->subtitle); ?></p>
                        <div class="mt-5 flex flex-wrap gap-3 sm:mt-7">
                            <a href="<?php echo e($banner->cta_url ?: route('shop.index')); ?>" class="btn-primary"><?php echo e($banner->cta_label ?: 'Shop Now'); ?></a>
                            <a href="<?php echo e(route('shop.new')); ?>" class="btn-outline">Explore Collection</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="absolute inset-0">
                <img src="https://placehold.co/1600x700/e6f2cf/000066?text=VanzaPack" alt="" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-white/10 md:to-transparent"></div>
                <div class="container-page relative flex h-full flex-col justify-center">
                    <div class="max-w-xl">
                        <h1 class="font-display text-2xl font-extrabold text-brand-800 sm:text-3xl lg:text-5xl">Your Destination for Quality Packaging</h1>
                        <p class="mt-3 max-w-md text-sm text-slate-600 sm:text-base">Bagasse, cups, tissue and grocery essentials — everything your business needs in one place.</p>
                        <a href="<?php echo e(route('shop.index')); ?>" class="btn-primary mt-5">Shop Now</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($heroBanners->count() > 1): ?>
            <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                <?php $__currentLoopData = $heroBanners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button @click="i = <?php echo e($index); ?>" :class="i === <?php echo e($index); ?> ? 'bg-brand-700 w-6' : 'bg-white/70'"
                            class="h-2 w-2 rounded-full shadow transition-all" aria-label="Slide <?php echo e($index + 1); ?>"></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </section>

    
    <section class="container-page grid grid-cols-2 gap-4 py-6 lg:grid-cols-4">
        <?php $__currentLoopData = [
            ['🚚', 'Fast UAE Delivery', 'On-time across all Emirates'],
            ['🔒', 'Secure Payments', 'COD & bank transfer'],
            ['↩️', 'Easy Returns', '7-day return window'],
            ['💬', 'Business Support', 'Help choosing the right products'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $title, $sub]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card flex items-center gap-3 p-4">
                <span class="text-2xl"><?php echo e($icon); ?></span>
                <div>
                    <p class="text-sm font-semibold text-brand-800"><?php echo e($title); ?></p>
                    <p class="text-xs text-slate-400"><?php echo e($sub); ?></p>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    
    <?php if($featuredCategories->isNotEmpty() || $brands->isNotEmpty()): ?>
        <section class="py-8">
            <?php if($featuredCategories->isNotEmpty()): ?>
                <div class="container-page mb-3 flex items-end justify-between">
                    <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['title' => 'Shop by Category']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Shop by Category']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
                    <a href="<?php echo e(route('shop.index')); ?>" class="link text-sm">View all →</a>
                </div>
                <div class="marquee mb-8 py-1" style="--marquee-duration: <?php echo e(max(24, $featuredCategories->count() * 6)); ?>s; --marquee-gap: 1rem;">
                    <?php $__currentLoopData = [1, 2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="marquee__track" aria-hidden="<?php echo e($pass === 2 ? 'true' : 'false'); ?>">
                            <?php $__currentLoopData = $featuredCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('category.show', $category->slug)); ?>"
                                   class="card group w-52 shrink-0 overflow-hidden text-center transition hover:-translate-y-1 hover:shadow-card-hover">
                                    <div class="aspect-video overflow-hidden bg-slate-50">
                                        <img src="<?php echo e($category->imageUrl()); ?>" alt="<?php echo e($category->name); ?>"
                                             class="h-full w-full object-cover transition group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-3">
                                        <p class="text-sm font-semibold text-brand-800"><?php echo e($category->name); ?></p>
                                        <p class="text-xs text-slate-400"><?php echo e($category->products_count); ?> products</p>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <?php if($brands->isNotEmpty()): ?>
                <div class="container-page mb-3 flex items-end justify-between">
                    <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['title' => 'Trusted Brands']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Trusted Brands']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
                    <a href="<?php echo e(route('brands.index')); ?>" class="link text-sm">All brands →</a>
                </div>
                <div class="marquee py-1" style="--marquee-duration: <?php echo e(max(20, $brands->count() * 5)); ?>s; --marquee-gap: 1rem;">
                    <?php $__currentLoopData = [1, 2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="marquee__track" aria-hidden="<?php echo e($pass === 2 ? 'true' : 'false'); ?>">
                            <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('brands.show', $brand->slug)); ?>"
                                   class="flex h-24 w-40 shrink-0 items-center justify-center rounded-xl border border-slate-100 bg-white p-4 transition hover:border-brand-500 hover:shadow-card">
                                    <?php if($brand->logo): ?>
                                        <img src="<?php echo e($brand->logoUrl()); ?>" alt="<?php echo e($brand->name); ?>" class="max-h-14 max-w-full object-contain" loading="lazy">
                                    <?php else: ?>
                                        <span class="text-center text-sm font-semibold text-brand-800"><?php echo e($brand->name); ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    
    <?php if($dealProducts->isNotEmpty()): ?>
        <section class="bg-brand-700 py-10">
            <div class="container-page">
                <div class="mb-6 flex items-center justify-between text-white">
                    <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['title' => '🔥 Mega Deals','first' => 'text-brand-300','rest' => 'text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => '🔥 Mega Deals','first' => 'text-brand-300','rest' => 'text-white']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
                    <a href="<?php echo e(route('shop.offers')); ?>" class="text-sm text-brand-300 hover:text-brand-200">View all →</a>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    <?php $__currentLoopData = $dealProducts->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginalfd88e5110b3e0874840c87825892a6cb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfd88e5110b3e0874840c87825892a6cb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront.product-row','data' => ['title' => 'New Arrivals','products' => $newArrivals,'link' => route('shop.new')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront.product-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'New Arrivals','products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newArrivals),'link' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('shop.new'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfd88e5110b3e0874840c87825892a6cb)): ?>
<?php $attributes = $__attributesOriginalfd88e5110b3e0874840c87825892a6cb; ?>
<?php unset($__attributesOriginalfd88e5110b3e0874840c87825892a6cb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfd88e5110b3e0874840c87825892a6cb)): ?>
<?php $component = $__componentOriginalfd88e5110b3e0874840c87825892a6cb; ?>
<?php unset($__componentOriginalfd88e5110b3e0874840c87825892a6cb); ?>
<?php endif; ?>

    
    <?php if($promoBanners->isNotEmpty()): ?>
        <section class="container-page py-4">
            <?php $__currentLoopData = $promoBanners->take(1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($promo->cta_url ?: route('shop.offers')); ?>"
                   class="block overflow-hidden rounded-2xl">
                    <img src="<?php echo e($promo->imageUrl()); ?>" alt="<?php echo e($promo->title); ?>" class="w-full object-cover">
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>
    <?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginalfd88e5110b3e0874840c87825892a6cb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfd88e5110b3e0874840c87825892a6cb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront.product-row','data' => ['title' => 'Best Sellers','products' => $bestSellers,'link' => route('shop.best')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront.product-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Best Sellers','products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bestSellers),'link' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('shop.best'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfd88e5110b3e0874840c87825892a6cb)): ?>
<?php $attributes = $__attributesOriginalfd88e5110b3e0874840c87825892a6cb; ?>
<?php unset($__attributesOriginalfd88e5110b3e0874840c87825892a6cb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfd88e5110b3e0874840c87825892a6cb)): ?>
<?php $component = $__componentOriginalfd88e5110b3e0874840c87825892a6cb; ?>
<?php unset($__componentOriginalfd88e5110b3e0874840c87825892a6cb); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginalfd88e5110b3e0874840c87825892a6cb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfd88e5110b3e0874840c87825892a6cb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront.product-row','data' => ['title' => 'Featured Products','products' => $featuredProducts,'link' => route('shop.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront.product-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Featured Products','products' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($featuredProducts),'link' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('shop.index'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfd88e5110b3e0874840c87825892a6cb)): ?>
<?php $attributes = $__attributesOriginalfd88e5110b3e0874840c87825892a6cb; ?>
<?php unset($__attributesOriginalfd88e5110b3e0874840c87825892a6cb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfd88e5110b3e0874840c87825892a6cb)): ?>
<?php $component = $__componentOriginalfd88e5110b3e0874840c87825892a6cb; ?>
<?php unset($__componentOriginalfd88e5110b3e0874840c87825892a6cb); ?>
<?php endif; ?>

    
    <section class="bg-brand-50 py-12">
        <div class="container-page">
            <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['title' => 'Why Choose '.settings('store_name', 'VanzaPack')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Why Choose '.settings('store_name', 'VanzaPack'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
            <div class="mt-6 grid gap-4 md:grid-cols-4">
                <?php $__currentLoopData = [
                    ['Quality Assurance', 'Premium materials and strict quality checks you can trust.'],
                    ['Competitive Pricing', 'Best value for your business with bulk discounts.'],
                    ['Fast Delivery Across UAE', 'Reliable and on-time delivery across all Emirates.'],
                    ['Business Support', 'Helping businesses choose the right products easily.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$t, $d]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl bg-white p-5 shadow-card">
                        <p class="font-semibold text-brand-700"><?php echo e($t); ?></p>
                        <p class="mt-1 text-sm text-slate-500"><?php echo e($d); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section class="bg-slate-50 py-12">
        <div class="container-page">
            <p class="text-xs font-semibold uppercase tracking-widest text-brand-600">Customer Stories</p>
            <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['title' => 'What Our Customers Say','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'What Our Customers Say','class' => 'mt-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <?php $__currentLoopData = [
                    ['Fatima Zahra', 'Cafe Manager, Abu Dhabi', 'Impressed with the product variety and competitive pricing. Everything we need with great quality and timely delivery.'],
                    ['Mohammed Tariq', 'Retail Business Owner, Sharjah', 'A dependable supplier for business essentials. Their wide range and professional support make ordering simple.'],
                    ['Sarah Mitchell', 'Hotel Procurement Manager, Dubai', 'Quality, consistency and service standards are excellent. Helps us maintain high standards every day.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name, $role, $quote]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <figure class="card p-6">
                        <div class="text-amber-400">★★★★★</div>
                        <blockquote class="mt-2 text-sm text-slate-600">“<?php echo e($quote); ?>”</blockquote>
                        <figcaption class="mt-4 text-sm">
                            <span class="font-semibold text-brand-800"><?php echo e($name); ?></span><br>
                            <span class="text-slate-400"><?php echo e($role); ?></span>
                        </figcaption>
                    </figure>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal15d9730126555fea898e8a62f8938736)): ?>
<?php $attributes = $__attributesOriginal15d9730126555fea898e8a62f8938736; ?>
<?php unset($__attributesOriginal15d9730126555fea898e8a62f8938736); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal15d9730126555fea898e8a62f8938736)): ?>
<?php $component = $__componentOriginal15d9730126555fea898e8a62f8938736; ?>
<?php unset($__componentOriginal15d9730126555fea898e8a62f8938736); ?>
<?php endif; ?>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/storefront/home.blade.php ENDPATH**/ ?>