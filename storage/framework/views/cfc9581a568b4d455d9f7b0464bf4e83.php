<?php if (isset($component)) { $__componentOriginal15d9730126555fea898e8a62f8938736 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal15d9730126555fea898e8a62f8938736 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront-layout','data' => ['title' => $pageTitle,'metaDescription' => $pageDescription,'breadcrumbs' => array_filter([
        ['label' => 'Shop', 'url' => route('shop.index')],
        ($activeCategory ?? null) ? ['label' => $activeCategory->name] : null,
        ($activeBrand ?? null) ? ['label' => $activeBrand->name] : null,
    ])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitle),'metaDescription' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageDescription),'breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(array_filter([
        ['label' => 'Shop', 'url' => route('shop.index')],
        ($activeCategory ?? null) ? ['label' => $activeCategory->name] : null,
        ($activeBrand ?? null) ? ['label' => $activeBrand->name] : null,
    ]))]); ?>

    <div class="container-page py-6" x-data="{ filtersOpen: false }">
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-brand-800"><?php echo e($pageTitle); ?></h1>
            <?php if($pageDescription): ?><p class="mt-1 text-sm text-slate-500"><?php echo e($pageDescription); ?></p><?php endif; ?>
        </div>

        <div class="lg:grid lg:grid-cols-[16rem_1fr] lg:gap-8">
            
            <aside class="hidden lg:block">
                <?php echo $__env->make('storefront.partials.shop-filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </aside>

            
            <div x-show="filtersOpen" x-cloak class="fixed inset-0 z-50 lg:hidden">
                <div class="absolute inset-0 bg-navy-900/40" @click="filtersOpen = false"></div>
                <div class="absolute left-0 top-0 h-full w-80 max-w-[85%] overflow-y-auto bg-white p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="font-semibold text-brand-800">Filters</span>
                        <button @click="filtersOpen = false" aria-label="Close">✕</button>
                    </div>
                    <?php echo $__env->make('storefront.partials.shop-filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>

            
            <div>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 bg-white p-3">
                    <div class="flex items-center gap-3">
                        <button class="btn-outline py-2 lg:hidden" @click="filtersOpen = true">Filters</button>
                        <p class="text-sm text-slate-500"><?php echo e($products->total()); ?> product<?php echo e($products->total() === 1 ? '' : 's'); ?></p>
                    </div>
                    <form method="GET" class="flex items-center gap-2">
                        <?php $__currentLoopData = request()->except(['sort', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(is_array($value)): ?>
                                <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($key); ?>[]" value="<?php echo e($v); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <label for="sort" class="text-sm text-slate-500">Sort</label>
                        <select id="sort" name="sort" class="field w-48 py-1.5 text-sm" onchange="this.form.submit()">
                            <?php $__currentLoopData = $sorts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if(request('sort') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </form>
                </div>

                <?php if($products->isEmpty()): ?>
                    <div class="card p-12 text-center text-slate-500">
                        <p>No products match your filters.</p>
                        <a href="<?php echo e(route('shop.index')); ?>" class="link mt-2 inline-block text-sm">Clear all filters</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                    <div class="mt-8"><?php echo e($products->links()); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/storefront/shop.blade.php ENDPATH**/ ?>