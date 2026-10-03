<?php

namespace App\Services\Payments;

use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Payment;
use InvalidArgumentException;

class PaymentManager
{
    /** @var array<string,PaymentGateway> */
    private array $gateways = [];

    public function __construct(
        CashOnDeliveryGateway $cod,
        BankTransferGateway $bank,
        ManualPaymentGateway $manual,
        OnlineGateway $online,
    ) {
        foreach ([$cod, $bank, $manual, $online] as $gateway) {
            $this->gateways[$gateway->key()] = $gateway;
        }
    }

    public function register(PaymentGateway $gateway): void
    {
        $this->gateways[$gateway->key()] = $gateway;
    }

    public function gateway(string $key): PaymentGateway
    {
        return $this->gateways[$key]
            ?? throw new InvalidArgumentException("Unknown payment gateway [{$key}].");
    }

    /** @return array<string,PaymentGateway> enabled gateways keyed by method */
    public function enabled(): array
    {
        return array_filter($this->gateways, fn (PaymentGateway $g) => $g->isEnabled());
    }

    /** Run payment for a new order and persist a Payment record. */
    public function charge(Order $order): PaymentResult
    {
        $method = $order->payment_method instanceof PaymentMethod
            ? $order->payment_method->value
            : $order->payment_method;

        $gateway = $this->gateway($method);
        $result = $gateway->process($order);

        Payment::create([
            'order_id' => $order->id,
            'method' => $gateway->key(),
            'status' => $result->status->value,
            'amount' => $order->grand_total,
            'currency' => $order->currency,
            'transaction_reference' => $result->reference,
            'meta' => $result->meta,
            'paid_at' => $result->status->value === 'paid' ? now() : null,
        ]);

        $order->update(['payment_status' => $result->status->value]);

        return $result;
    }
}
