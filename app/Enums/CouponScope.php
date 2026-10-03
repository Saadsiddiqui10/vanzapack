<?php

namespace App\Enums;

enum CouponScope: string
{
    case Global = 'global';
    case Product = 'product';
    case Category = 'category';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
