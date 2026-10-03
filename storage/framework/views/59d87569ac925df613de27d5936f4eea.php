<form method="GET" class="space-y-6 text-sm" x-data>
    
    <?php if(request('q')): ?><input type="hidden" name="q" value="<?php echo e(request('q')); ?>"><?php endif; ?>
    <?php if(request('sort')): ?><input type="hidden" name="sort" value="<?php echo e(request('sort')); ?>"><?php endif; ?>

    <div>
        <h3 class="mb-2 font-semibold text-brand-800">Categories</h3>
        <ul class="space-y-1">
            <?php $__currentLoopData = $filterCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e(route('category.show', $cat->slug)); ?>"
                       class="block rounded px-2 py-1 <?php echo e(($activeCategory?->id === $cat->id) ? 'bg-brand-50 font-semibold text-brand-700' : 'hover:bg-slate-50'); ?>">
                        <?php echo e($cat->name); ?>

                    </a>
                    <?php if($cat->children->isNotEmpty()): ?>
                        <ul class="ml-3 mt-1 space-y-1 border-l border-slate-100 pl-3">
                            <?php $__currentLoopData = $cat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a href="<?php echo e(route('category.show', $child->slug)); ?>"
                                       class="block rounded px-2 py-0.5 text-slate-500 <?php echo e(($activeCategory?->id === $child->id) ? 'font-semibold text-brand-700' : 'hover:text-brand-600'); ?>">
                                        <?php echo e($child->name); ?> <span class="text-xs text-slate-300">(<?php echo e($child->products_count); ?>)</span>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <div>
        <h3 class="mb-2 font-semibold text-brand-800">Price (<?php echo e(settings('currency', 'AED')); ?>)</h3>
        <div class="flex items-center gap-2">
            <input type="number" name="min_price" value="<?php echo e(request('min_price')); ?>" placeholder="<?php echo e($priceBounds['min']); ?>" class="field py-1.5">
            <span class="text-slate-400">–</span>
            <input type="number" name="max_price" value="<?php echo e(request('max_price')); ?>" placeholder="<?php echo e($priceBounds['max']); ?>" class="field py-1.5">
        </div>
    </div>

    <div>
        <h3 class="mb-2 font-semibold text-brand-800">Brand</h3>
        <ul class="max-h-48 space-y-1 overflow-y-auto pr-1">
            <?php $__currentLoopData = $filterBrands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="brands[]" value="<?php echo e($brand->slug); ?>"
                               <?php if(in_array($brand->slug, (array) request('brands', []))): echo 'checked'; endif; ?>
                               class="rounded border-slate-300 text-brand-500 focus:ring-brand-500">
                        <span><?php echo e($brand->name); ?></span>
                    </label>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <?php $__currentLoopData = $filterAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div>
            <h3 class="mb-2 font-semibold text-brand-800"><?php echo e($attribute->name); ?></h3>
            <div class="flex flex-wrap gap-1.5">
                <?php $__currentLoopData = $attribute->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $checked = in_array((string) $value->id, (array) request('attributes', [])); ?>
                    <label class="cursor-pointer">
                        <input type="checkbox" name="attributes[]" value="<?php echo e($value->id); ?>" <?php if($checked): echo 'checked'; endif; ?> class="peer sr-only">
                        <span class="badge border <?php echo e($checked ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-500'); ?>">
                            <?php echo e($value->value); ?>

                        </span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div>
        <h3 class="mb-2 font-semibold text-brand-800">Rating</h3>
        <?php $__currentLoopData = [4 => '4 stars & up', 3 => '3 stars & up', 2 => '2 stars & up']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stars => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="flex items-center gap-2 py-0.5">
                <input type="radio" name="rating" value="<?php echo e($stars); ?>" <?php if((int) request('rating') === $stars): echo 'checked'; endif; ?>
                       class="border-slate-300 text-brand-500 focus:ring-brand-500">
                <span><?php echo e($label); ?></span>
            </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="space-y-1.5">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="availability" value="in_stock" <?php if(request('availability') === 'in_stock'): echo 'checked'; endif; ?>
                   class="rounded border-slate-300 text-brand-500 focus:ring-brand-500">
            <span>In stock only</span>
        </label>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="on_sale" value="1" <?php if(request()->boolean('on_sale')): echo 'checked'; endif; ?>
                   class="rounded border-slate-300 text-brand-500 focus:ring-brand-500">
            <span>On sale</span>
        </label>
    </div>

    <div class="flex gap-2">
        <button class="btn-primary flex-1 py-2">Apply</button>
        <a href="<?php echo e(url()->current()); ?>" class="btn-outline py-2">Reset</a>
    </div>
</form>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/storefront/partials/shop-filters.blade.php ENDPATH**/ ?>