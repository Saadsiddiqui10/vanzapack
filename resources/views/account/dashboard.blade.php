<x-account-layout title="Dashboard">
    @if(auth()->user()->must_change_password)
        <div class="mb-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">
            For security, please <a href="{{ route('account.profile') }}" class="font-semibold underline">change your password</a>.
        </div>
    @endif

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        @foreach([
            ['Orders', $ordersCount, route('account.orders')],
            ['Open orders', $openOrders, route('account.orders')],
            ['Wishlist', $wishlistCount, route('wishlist.index')],
            ['Addresses', $addressCount, route('account.addresses.index')],
        ] as [$label, $value, $url])
            <a href="{{ $url }}" class="card p-4 hover:shadow-card-hover">
                <p class="text-2xl font-bold text-brand-800">{{ $value }}</p>
                <p class="text-xs text-slate-400">{{ $label }}</p>
            </a>
        @endforeach
    </div>

    <div class="card mt-6 p-5">
        <h2 class="mb-3 font-semibold text-brand-800">Recent orders</h2>
        @forelse($recentOrders as $order)
            <a href="{{ route('account.orders.show', $order->number) }}" class="flex items-center justify-between border-b border-slate-100 py-3 text-sm last:border-0">
                <span class="font-medium text-brand-800">{{ $order->number }}</span>
                <span class="text-slate-400">{{ $order->created_at->format('d M Y') }}</span>
                <span class="badge {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span>
                <span class="font-semibold">{{ money($order->grand_total) }}</span>
            </a>
        @empty
            <p class="text-sm text-slate-400">No orders yet. <a href="{{ route('shop.index') }}" class="link">Start shopping</a>.</p>
        @endforelse
    </div>
</x-account-layout>
