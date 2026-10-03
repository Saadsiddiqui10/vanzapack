<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product']));

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

foreach (array_filter((['product']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($product->isOnSale()): ?>
    <span class="font-semibold text-brand-800"><?php echo e(money($product->currentPrice())); ?></span>
    <span class="text-sm text-slate-400 line-through"><?php echo e(money($product->price)); ?></span>
    <span class="badge bg-rose-100 text-rose-700">-<?php echo e($product->discountPercent()); ?>%</span>
<?php else: ?>
    <span class="font-semibold text-brand-800"><?php echo e(money($product->currentPrice())); ?></span>
<?php endif; ?>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/components/price.blade.php ENDPATH**/ ?>