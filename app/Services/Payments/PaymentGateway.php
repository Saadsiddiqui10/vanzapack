<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentGateway
{
    /** Machine key, e.g. "cod". */
    public function key(): string;

    /** Human label shown at checkout. */
    public function label(): string;

    /** Whether the gateway is currently enabled in settings. */
    public function isEnabled(): bool;

    /**
     * Begin payment for a freshly created order.
     * Return a PaymentResult describing what should happen next.
     */
    public function process(Order $order): PaymentResult;
}
