<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Restock = 'restock';
    case Sale = 'sale';
    case Adjustment = 'adjustment';
    case ReturnToStock = 'return';
    case Reservation = 'reservation';
    case ReleaseReservation = 'release_reservation';

    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }
}
