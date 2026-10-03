@props(['title' => 'Admin', 'active' => null])

@php
    $nav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'key' => 'dashboard', 'perm' => null],
        ['heading' => 'Catalog'],
        ['label' => 'Products', 'route' => 'admin.products.index', 'key' => 'products', 'perm' => 'products.view'],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'key' => 'categories', 'perm' => 'categories.view'],
        ['label' => 'Brands', 'route' => 'admin.brands.index', 'key' => 'brands', 'perm' => 'brands.view'],
        ['label' => 'Attributes', 'route' => 'admin.attributes.index', 'key' => 'attributes', 'perm' => 'attributes.view'],
        ['label' => 'Inventory', 'route' => 'admin.inventory.index', 'key' => 'inventory', 'perm' => 'inventory.view'],
        ['label' => 'Reviews', 'route' => 'admin.reviews.index', 'key' => 'reviews', 'perm' => 'reviews.view'],
        ['heading' => 'Sales'],
        ['label' => 'Orders', 'route' => 'admin.orders.index', 'key' => 'orders', 'perm' => 'orders.view'],
        ['label' => 'Customers', 'route' => 'admin.customers.index', 'key' => 'customers', 'perm' => 'customers.view'],
        ['label' => 'Reports', 'route' => 'admin.reports.index', 'key' => 'reports', 'perm' => 'reports.view'],
        ['heading' => 'Marketing'],
        ['label' => 'Coupons', 'route' => 'admin.coupons.index', 'key' => 'coupons', 'perm' => 'coupons.view'],
        ['label' => 'Banners', 'route' => 'admin.banners.index', 'key' => 'banners', 'perm' => 'banners.view'],
        ['label' => 'Announcement Bar', 'route' => 'admin.announcements.index', 'key' => 'announcements', 'perm' => 'banners.view'],
        ['label' => 'Navigation Menu', 'route' => 'admin.menu-items.index', 'key' => 'menu', 'perm' => 'banners.view'],
        ['label' => 'Pages', 'route' => 'admin.pages.index', 'key' => 'pages', 'perm' => 'pages.view'],
        ['label' => 'Newsletter', 'route' => 'admin.newsletter.index', 'key' => 'newsletter', 'perm' => 'newsletter.view'],
        ['label' => 'Messages', 'route' => 'admin.messages.index', 'key' => 'messages', 'perm' => null],
        ['heading' => 'Configuration'],
        ['label' => 'Shipping', 'route' => 'admin.shipping-methods.index', 'key' => 'shipping', 'perm' => 'shipping.view'],
        ['label' => 'Tax', 'route' => 'admin.tax-rates.index', 'key' => 'tax', 'perm' => 'tax.view'],
        ['label' => 'Settings', 'route' => 'admin.settings.edit', 'key' => 'settings', 'perm' => 'settings.manage'],
        ['heading' => 'System'],
        ['label' => 'Admin Users', 'route' => 'admin.users.index', 'key' => 'users', 'perm' => 'users.manage'],
        ['label' => 'Roles', 'route' => 'admin.roles.index', 'key' => 'roles', 'perm' => 'users.manage'],
        ['label' => 'Activity Log', 'route' => 'admin.activity.index', 'key' => 'activity', 'perm' => 'activity.view'],
    ];
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ settings('store_name', 'VanzaPack') }} Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 text-slate-700" x-data="{ sidebar: false }">
<div class="flex min-h-full">
    {{-- Sidebar --}}
    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full overflow-y-auto bg-navy-600 text-slate-200 transition-transform lg:static lg:translate-x-0">
        <div class="px-4 py-4">
            <div class="rounded-lg bg-white px-3 py-2">
                <img src="{{ asset('images/logo.png') }}" alt="VanzaPack" class="h-9 w-auto">
            </div>
        </div>
        <nav class="px-3 pb-10 text-sm">
            @foreach($nav as $item)
                @if(isset($item['heading']))
                    <p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wide text-white/40">{{ $item['heading'] }}</p>
                @elseif(is_null($item['perm']) || $user->hasPermission($item['perm']))
                    <a href="{{ route($item['route']) }}"
                       class="block rounded-lg px-3 py-2 {{ $active === $item['key'] ? 'bg-white/15 font-semibold text-white' : 'text-slate-300 hover:bg-white/10' }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    <div x-show="sidebar" @click="sidebar=false" class="fixed inset-0 z-30 bg-black/30 lg:hidden" x-cloak></div>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3">
            <button class="lg:hidden" @click="sidebar = true" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="font-display text-lg font-bold text-brand-800">{{ $title }}</h1>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('home') }}" target="_blank" class="text-slate-400 hover:text-brand-600">View store ↗</a>
                <span class="hidden text-slate-500 sm:inline">{{ $user->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-rose-500 hover:underline">Logout</button></form>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">Please fix the following:</p>
                    <ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
