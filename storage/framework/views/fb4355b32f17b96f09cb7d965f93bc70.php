<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('code'); ?> — <?php echo e(config('app.name', 'VanzaPack')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 p-6 text-slate-700">
    <div class="max-w-md text-center">
        <a href="<?php echo e(url('/')); ?>" class="mb-6 inline-flex items-center gap-2">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-500 font-display text-xl font-extrabold text-white">V</span>
            <span class="font-display text-2xl font-extrabold text-brand-800">Vanza<span class="text-brand-500">Pack</span></span>
        </a>
        <p class="font-display text-6xl font-extrabold text-brand-500"><?php echo $__env->yieldContent('code'); ?></p>
        <h1 class="mt-2 font-display text-xl font-bold text-brand-800"><?php echo $__env->yieldContent('title'); ?></h1>
        <p class="mt-2 text-sm text-slate-500"><?php echo $__env->yieldContent('message'); ?></p>
        <a href="<?php echo e(url('/')); ?>" class="btn-primary mt-6">Back to store</a>
    </div>
</body>
</html>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/errors/layout.blade.php ENDPATH**/ ?>