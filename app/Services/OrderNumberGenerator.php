<?php

namespace App\Services;

use App\Models\Order;

class OrderNumberGenerator
{
    /**
     * Produce a unique, human-readable order number: ORD-2026-000123
     */
    public function generate(): string
    {
        $prefix = settings('order_prefix', 'ORD');
        $year = now()->format('Y');

        $lastNumber = Order::query()
            ->where('number', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->value('number');

        $sequence = $lastNumber
            ? ((int) substr($lastNumber, strrpos($lastNumber, '-') + 1)) + 1
            : 1;

        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
    }
}
