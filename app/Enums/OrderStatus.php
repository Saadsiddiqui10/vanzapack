<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Packed = 'packed';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Returned = 'returned';
    case Refunded = 'refunded';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Confirmed, self::Processing, self::Packed => 'blue',
            self::Shipped => 'indigo',
            self::Delivered => 'green',
            self::Cancelled, self::Returned => 'red',
            self::Refunded => 'gray',
        };
    }

    /** Full Tailwind utility classes (kept literal so JIT picks them up). */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Confirmed, self::Processing, self::Packed => 'bg-sky-100 text-sky-800',
            self::Shipped => 'bg-indigo-100 text-indigo-800',
            self::Delivered => 'bg-brand-100 text-brand-800',
            self::Cancelled, self::Returned => 'bg-rose-100 text-rose-700',
            self::Refunded => 'bg-slate-100 text-slate-700',
        };
    }

    /** Statuses that release/deduct inventory or block further edits. */
    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled, self::Returned, self::Refunded], true);
    }

    public static function options(): array
    {
        return array_map(fn (self $s) => ['value' => $s->value, 'label' => $s->label()], self::cases());
    }
}
