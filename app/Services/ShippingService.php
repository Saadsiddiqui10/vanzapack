<?php

namespace App\Services;

use App\Models\ShippingMethod;
use Illuminate\Support\Collection;

class ShippingService
{
    /** @return Collection<int,ShippingMethod> */
    public function availableMethods(float $subtotal, string $countryCode = 'AE'): Collection
    {
        return ShippingMethod::active()
            ->get()
            ->filter(fn (ShippingMethod $m) => $m->country === null || $m->country === $countryCode)
            ->values();
    }

    public function costFor(ShippingMethod $method, float $subtotal): float
    {
        return round($method->costFor($subtotal), 2);
    }

    public function resolve(?int $methodId, float $subtotal, string $countryCode = 'AE'): ?ShippingMethod
    {
        $methods = $this->availableMethods($subtotal, $countryCode);

        return $methods->firstWhere('id', $methodId) ?? $methods->first();
    }
}
