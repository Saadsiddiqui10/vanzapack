<?php

namespace App\Services\Payments;

use App\Models\Order;

/**
 * Placeholder for a hosted online gateway (Stripe / Telr / Network / PayTabs …).
 * Swap process() for a real API call + webhook handler when a provider is chosen.
 */
class OnlineGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'online';
    }

    public function label(): string
    {
        return 'Pay Online (Card)';
    }

    public function isEnabled(): bool
    {
        return (bool) settings('payment_online_enabled', false);
    }

    public function process(Order $order): PaymentResult
    {
        // Real implementation would create a hosted-checkout session and return its URL.
        return PaymentResult::redirect(route('checkout.online.mock', $order));
    }
}
