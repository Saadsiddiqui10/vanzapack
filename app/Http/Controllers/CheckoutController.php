<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Exceptions\CheckoutException;
use App\Http\Requests\Shop\PlaceOrderRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\Payments\PaymentManager;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CheckoutService $checkout,
        private readonly ShippingService $shipping,
        private readonly PaymentManager $payments,
    ) {}

    public function index(Request $request)
    {
        $cart = $this->cart->current(false);

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if (! settings('allow_guest_checkout', true) && ! $request->user()) {
            return redirect()->guest(route('login'))->with('error', 'Please sign in to check out.');
        }

        $subtotal = round($cart->items->sum(fn ($i) => $i->lineTotal()), 2);
        $shippingMethods = $this->shipping->availableMethods($subtotal);
        $selectedMethodId = (int) $request->input('shipping_method_id', $shippingMethods->first()?->id);

        $totals = $this->cart->totals($selectedMethodId);

        return view('storefront.checkout', [
            'cart' => $cart,
            'totals' => $totals,
            'shippingMethods' => $shippingMethods,
            'selectedMethodId' => $selectedMethodId,
            'gateways' => $this->payments->enabled(),
            'addresses' => $request->user()?->addresses ?? collect(),
            'bankDetails' => settings('payment_bank_details'),
        ]);
    }

    public function store(PlaceOrderRequest $request)
    {
        $cart = $this->cart->current(false);

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        try {
            $outcome = $this->checkout->place($cart, $request->payload(), $request->user());
        } catch (CheckoutException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        /** @var Order $order */
        $order = $outcome['order'];
        $result = $outcome['payment'];

        if ($request->user() && $request->boolean('save_address')) {
            $ship = $request->input('ship');
            $request->user()->addresses()->create([
                'label' => 'Delivery',
                'first_name' => $ship['first_name'], 'last_name' => $ship['last_name'],
                'phone' => $ship['phone'], 'line1' => $ship['line1'], 'line2' => $ship['line2'] ?? null,
                'city' => $ship['city'], 'state' => $ship['state'] ?? null,
                'postal_code' => $ship['postal_code'] ?? null, 'country' => $ship['country'],
            ]);
        }

        if ($result->redirectUrl) {
            return redirect()->to($result->redirectUrl);
        }

        return redirect()->route('checkout.confirmation', $order->number);
    }

    public function confirmation(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);
        $order->load(['items', 'payment', 'shippingMethod']);

        return view('storefront.checkout-confirmation', compact('order'));
    }

    public function mockOnline(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);

        return view('storefront.checkout-mock-pay', compact('order'));
    }

    public function mockOnlineConfirm(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);

        $payment = $order->payment;
        $payment?->update([
            'status' => PaymentStatus::Paid->value,
            'transaction_reference' => 'MOCK-'.strtoupper(bin2hex(random_bytes(4))),
            'paid_at' => now(),
        ]);
        $order->update(['payment_status' => PaymentStatus::Paid->value]);

        return redirect()->route('checkout.confirmation', $order->number)
            ->with('success', 'Payment received. Thank you!');
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        if ($order->user_id) {
            abort_unless($request->user()?->id === $order->user_id, 403);

            return;
        }

        // Guest order: allow for a short window after creation.
        abort_unless($order->created_at->gt(now()->subHours(4)), 403);
    }
}
