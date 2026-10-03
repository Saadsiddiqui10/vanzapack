<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => '',
    'first' => 'text-brand-500',   // primary colour word(s)
    'rest' => 'text-navy-600',      // secondary colour word(s)
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
    'title' => '',
    'first' => 'text-brand-500',   // primary colour word(s)
    'rest' => 'text-navy-600',      // secondary colour word(s)
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // strip a leading emoji/symbol so colouring lands on the actual words
    $clean = trim(preg_replace('/^\p{So}\p{Sk}*\s*/u', '', $title));
    $lead = mb_substr($title, 0, mb_strlen($title) - mb_strlen($clean));
    $parts = explode(' ', $clean, 2);
?>

<h2 <?php echo e($attributes->merge(['class' => 'font-display text-2xl font-bold'])); ?>>
    <?php if($lead): ?><?php echo e($lead); ?><?php endif; ?><span class="<?php echo e($first); ?>"><?php echo e($parts[0]); ?></span><?php if(isset($parts[1])): ?> <span class="<?php echo e($rest); ?>"><?php echo e($parts[1]); ?></span><?php endif; ?>
</h2>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/components/section-heading.blade.php ENDPATH**/ ?>