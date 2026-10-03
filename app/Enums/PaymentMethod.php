<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CashOnDelivery = 'cod';
    case BankTransfer = 'bank_transfer';
    case Manual = 'manual';
    case Online = 'online';

    public function label(): string
    {
        return match ($this) {
            self::CashOnDelivery => 'Cash on Delivery',
            self::BankTransfer => 'Bank Transfer',
            self::Manual => 'Manual Payment',
            self::Online => 'Online Payment',
        };
    }
}
