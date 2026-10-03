<?php

namespace App\Services\Payments;

use App\Models\Order;

class BankTransferGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'bank_transfer';
    }

    public function label(): string
    {
        return 'Bank Transfer';
    }

    public function isEnabled(): bool
    {
        return (bool) settings('payment_bank_transfer_enabled', true);
    }

    public function process(Order $order): PaymentResult
    {
        return PaymentResult::awaitingConfirmation(
            'Transfer '.money($order->grand_total)." to our account and quote {$order->number}. "
            .'Your order ships once payment is confirmed.'
        );
    }
}
