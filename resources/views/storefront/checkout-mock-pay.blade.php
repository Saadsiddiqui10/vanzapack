<x-storefront-layout title="Complete Payment">
    <div class="container-page py-12">
        <div class="mx-auto max-w-md card p-6 text-center">
            <h1 class="font-display text-xl font-bold text-brand-800">Mock Payment Gateway</h1>
            <p class="mt-2 text-sm text-slate-500">
                This is a placeholder for a real hosted checkout (Stripe / Telr / Network / PayTabs).
                In production, swap <code>OnlineGateway::process()</code> for a real session + webhook.
            </p>
            <p class="mt-4 text-2xl font-bold text-brand-800">{{ money($order->grand_total) }}</p>
            <p class="text-xs text-slate-400">Order {{ $order->number }}</p>

            <form method="POST" action="{{ route('checkout.online.mock.confirm', $order->number) }}" class="mt-6">
                @csrf
                <button class="btn-primary w-full">Pay now (simulate success)</button>
            </form>
            <a href="{{ route('checkout.confirmation', $order->number) }}" class="mt-2 inline-block text-xs text-slate-400 hover:underline">Pay later</a>
        </div>
    </div>
</x-storefront-layout>
