@props(['title' => 'My Account'])

<x-storefront-layout :title="$title" :breadcrumbs="[['label' => 'Account', 'url' => route('account.dashboard')], ['label' => $title]]">
    <div class="container-page py-8">
        <div class="lg:grid lg:grid-cols-[15rem_1fr] lg:gap-8">
            <aside class="mb-6 lg:mb-0">
                <div class="card p-4">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 font-semibold text-brand-800">
                            {{ auth()->user()->initials() }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-brand-800">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    @php
                        $links = [
                            ['account.dashboard', 'Dashboard'],
                            ['account.orders', 'Orders'],
                            ['account.addresses.index', 'Addresses'],
                            ['wishlist.index', 'Wishlist'],
                            ['account.reviews', 'Reviews'],
                            ['account.notifications', 'Notifications'],
                            ['account.profile', 'Profile & Password'],
                        ];
                    @endphp
                    <nav class="mt-3 space-y-1 text-sm">
                        @foreach($links as [$route, $label])
                            <a href="{{ route($route) }}"
                               class="block rounded-lg px-3 py-2 {{ request()->routeIs($route) || request()->routeIs($route.'.*') ? 'bg-brand-50 font-semibold text-brand-700' : 'text-slate-600 hover:bg-slate-50' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="block w-full rounded-lg px-3 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">Log out</button>
                        </form>
                        @if(auth()->user()->isStaff())
                            <a href="{{ url('/admin') }}" class="mt-2 block rounded-lg bg-brand-700 px-3 py-2 text-center text-white">Admin panel →</a>
                        @endif
                    </nav>
                </div>
            </aside>

            <div>
                <h1 class="mb-4 font-display text-2xl font-bold text-brand-800">{{ $title }}</h1>
                {{ $slot }}
            </div>
        </div>
    </div>
</x-storefront-layout>
