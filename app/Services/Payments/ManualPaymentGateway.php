<?php

namespace App\Services\Payments;

use App\Models\Order;

class ManualPaymentGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'manual';
    }

    public function label(): string
    {
        return 'Manual Payment';
    }

    public function isEnabled(): bool
    {
        return (bool) settings('payment_manual_enabled', false);
    }

    public function process(Order $order): PaymentResult
    {
        return PaymentResult::awaitingConfirmation('Our team will contact you to arrange payment.');
    }
}
