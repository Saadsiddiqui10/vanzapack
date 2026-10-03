<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'VanzaPack')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen bg-slate-50 text-slate-700 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <a href="<?php echo e(route('home')); ?>" class="mb-6 flex items-center">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="VanzaPack" class="h-14 w-auto">
        </a>

        <div class="w-full max-w-md rounded-2xl border border-slate-100 bg-white p-8 shadow-card">
            <?php echo e($slot); ?>

        </div>

        <a href="<?php echo e(route('home')); ?>" class="mt-6 text-sm text-slate-400 hover:text-brand-600">← Back to store</a>
    </div>
</body>
</html>
<?php /**PATH D:\Saad\work\Final build\greencrate - Copy\resources\views/layouts/guest.blade.php ENDPATH**/ ?>