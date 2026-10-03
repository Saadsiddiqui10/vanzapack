<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['rating' => 0, 'count' => null, 'size' => 'sm']));

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

foreach (array_filter((['rating' => 0, 'count' => null, 'size' => 'sm']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $rating = (float) $rating;
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5;
    $dim = $size === 'lg' ? 'h-5 w-5' : 'h-4 w-4';
?>

<div <?php echo e($attributes->merge(['class' => 'inline-flex items-center gap-1'])); ?> aria-label="Rated <?php echo e(number_format($rating, 1)); ?> out of 5">
    <div class="flex text-amber-400">
        <?php for($i = 1; $i <= 5; $i++): ?>
            <svg class="<?php echo e($dim); ?>" viewBox="0 0 20 20" fill="<?php echo e($i <= $full || ($i === $full + 1 && $half) ? 'currentColor' : 'none'); ?>" stroke="currentColor">
                <path stroke-width="1.5" d="M10 1.5l2.6 5.3 5.9.9-4.2 4.1 1 5.8L10 15l-5.3 2.8 1-5.8L1.5 7.7l5.9-.9L10 1.5z"/>
            </svg>
        <?php endfor; ?>
    </div>
    <?php if($count !== null): ?>
        <span class="text-xs text-slate-400">(<?php echo e($count); ?>)</span>
    <?php endif; ?>
</div>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/components/rating-stars.blade.php ENDPATH**/ ?>