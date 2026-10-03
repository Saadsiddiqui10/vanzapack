<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'inWishlist' => false]));

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

foreach (array_filter((['product', 'inWishlist' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $secondImage = $product->images->get(1);
    $out = ! $product->inStock();
?>

<div class="group card relative flex flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-card-hover">
    
    <div class="absolute left-3 top-3 z-10 flex flex-col gap-1">
        <?php if($product->isOnSale()): ?>
            <span class="badge bg-rose-500 text-white">-<?php echo e($product->discountPercent()); ?>%</span>
        <?php endif; ?>
        <?php if($product->is_new_arrival): ?>
            <span class="badge bg-brand-700 text-white">New</span>
        <?php endif; ?>
        <?php if($out): ?>
            <span class="badge bg-slate-700 text-white">Sold out</span>
        <?php endif; ?>
    </div>

    <button type="button"
            x-data="wishlistButton(<?php echo \Illuminate\Support\Js::from($product->slug)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($inWishlist)->toHtml() ?>)"
            @click.prevent="toggle()"
            :class="inList ? 'text-rose-500' : 'text-slate-300 hover:text-rose-400'"
            class="absolute right-3 top-3 z-10 rounded-full bg-white/90 p-1.5 shadow transition"
            aria-label="Toggle wishlist">
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21s-7-4.35-9.5-8.5C1 9 3 5.5 6.5 5.5 8.7 5.5 10.5 7 12 9c1.5-2 3.3-3.5 5.5-3.5C21 5.5 23 9 21.5 12.5 19 16.65 12 21 12 21z"/></svg>
    </button>

    <a href="<?php echo e($product->url()); ?>" class="relative block aspect-square overflow-hidden bg-slate-50">
        <img src="<?php echo e($product->primaryImageUrl()); ?>" alt="<?php echo e($product->name); ?>" loading="lazy"
             class="h-full w-full object-cover transition duration-500 <?php echo e($secondImage ? 'group-hover:opacity-0' : 'group-hover:scale-105'); ?>">
        <?php if($secondImage): ?>
            <img src="<?php echo e($secondImage->url()); ?>" alt="" aria-hidden="true" loading="lazy"
                 class="absolute inset-0 h-full w-full object-cover opacity-0 transition duration-500 group-hover:opacity-100">
        <?php endif; ?>
        <span class="absolute inset-x-3 bottom-3 translate-y-2 opacity-0 transition group-hover:translate-y-0 group-hover:opacity-100">
            <button type="button" @click.prevent="$dispatch('quick-view', <?php echo \Illuminate\Support\Js::from($product->slug)->toHtml() ?>)"
                    class="btn-outline w-full bg-white/95 py-2 text-xs">Quick view</button>
        </span>
    </a>

    <div class="flex flex-1 flex-col p-4">
        <?php if($product->brand): ?>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400"><?php echo e($product->brand->name); ?></p>
        <?php endif; ?>
        <a href="<?php echo e($product->url()); ?>" class="mt-1 line-clamp-2 text-sm font-medium text-brand-800 hover:text-brand-600">
            <?php echo e($product->name); ?>

        </a>

        <div class="mt-2">
            <?php if (isset($component)) { $__componentOriginal077a61d60611f096a94f8e1725d6bb16 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal077a61d60611f096a94f8e1725d6bb16 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rating-stars','data' => ['rating' => $product->rating_avg,'count' => $product->reviews_count ?? $product->rating_count]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rating-stars'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rating' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->rating_avg),'count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->reviews_count ?? $product->rating_count)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal077a61d60611f096a94f8e1725d6bb16)): ?>
<?php $attributes = $__attributesOriginal077a61d60611f096a94f8e1725d6bb16; ?>
<?php unset($__attributesOriginal077a61d60611f096a94f8e1725d6bb16); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal077a61d60611f096a94f8e1725d6bb16)): ?>
<?php $component = $__componentOriginal077a61d60611f096a94f8e1725d6bb16; ?>
<?php unset($__componentOriginal077a61d60611f096a94f8e1725d6bb16); ?>
<?php endif; ?>
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal5c7c50258000edf57abfef324d310474 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c7c50258000edf57abfef324d310474 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.price','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('price'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5c7c50258000edf57abfef324d310474)): ?>
<?php $attributes = $__attributesOriginal5c7c50258000edf57abfef324d310474; ?>
<?php unset($__attributesOriginal5c7c50258000edf57abfef324d310474); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5c7c50258000edf57abfef324d310474)): ?>
<?php $component = $__componentOriginal5c7c50258000edf57abfef324d310474; ?>
<?php unset($__componentOriginal5c7c50258000edf57abfef324d310474); ?>
<?php endif; ?>
        </div>

        <div class="mt-3 pt-1">
            <?php if($product->has_variants): ?>
                <a href="<?php echo e($product->url()); ?>" class="btn-navy w-full py-2 text-xs">Choose options</a>
            <?php else: ?>
                <form x-data="addToCart(<?php echo \Illuminate\Support\Js::from($product->id)->toHtml() ?>)" @submit.prevent="submit" class="w-full">
                    <button class="btn-navy w-full py-2 text-xs" :disabled="busy || <?php echo e($out ? 'true' : 'false'); ?>"
                            x-text="busy ? 'Adding…' : '<?php echo e($out ? 'Out of stock' : 'Add to cart'); ?>'"></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/components/product-card.blade.php ENDPATH**/ ?>