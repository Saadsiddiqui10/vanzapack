<x-account-layout title="Orders">
    @if($orders->isEmpty())
        <div class="card p-12 text-center text-slate-500">
            <p>You haven't placed any orders yet.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary mt-4">Start shopping</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($orders as $order)
                <a href="{{ route('account.orders.show', $order->number) }}" class="card block p-4 hover:shadow-card-hover">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="font-semibold text-brand-800">{{ $order->number }}</p>
                            <p class="text-xs text-slate-400">{{ $order->created_at->format('d M Y') }} · {{ $order->items_count }} item(s)</p>
                        </div>
                        <span class="badge {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span>
                        <span class="badge {{ $order->payment_status->badgeClasses() }}">{{ $order->payment_status->label() }}</span>
                        <span class="font-semibold text-brand-800">{{ money($order->grand_total) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-account-layout>
