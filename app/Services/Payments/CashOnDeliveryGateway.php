<?php

namespace App\Services\Payments;

use App\Models\Order;

class CashOnDeliveryGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'cod';
    }

    public function label(): string
    {
        return 'Cash on Delivery';
    }

    public function isEnabled(): bool
    {
        return (bool) settings('payment_cod_enabled', true);
    }

    public function process(Order $order): PaymentResult
    {
        return PaymentResult::pending('Pay in cash when your order is delivered.');
    }
}
