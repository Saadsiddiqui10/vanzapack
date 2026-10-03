<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case AwaitingConfirmation = 'awaiting_confirmation';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';

    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending, self::AwaitingConfirmation => 'amber',
            self::Paid => 'green',
            self::Failed => 'red',
            self::Refunded, self::PartiallyRefunded => 'gray',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending, self::AwaitingConfirmation => 'bg-amber-100 text-amber-800',
            self::Paid => 'bg-brand-100 text-brand-800',
            self::Failed => 'bg-rose-100 text-rose-700',
            self::Refunded, self::PartiallyRefunded => 'bg-slate-100 text-slate-700',
        };
    }
}
