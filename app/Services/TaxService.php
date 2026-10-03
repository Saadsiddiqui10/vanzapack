<?php

namespace App\Services;

use App\Models\TaxRate;
use Illuminate\Support\Facades\Cache;

class TaxService
{
    public function rateFor(string $countryCode = 'AE', string $taxClass = 'standard'): float
    {
        if (! settings('tax_enabled', true)) {
            return 0.0;
        }

        $rates = Cache::remember('tax.rates', 600, fn () => TaxRate::active()->orderByDesc('priority')->get());

        $match = $rates->first(fn (TaxRate $r) => ($r->country === null || $r->country === $countryCode)
            && ($r->tax_class === $taxClass));

        return $match ? (float) $match->rate : (float) settings('default_tax_rate', 0);
    }

    /** Tax amount for a taxable base (after discount, before shipping). */
    public function calculate(float $taxableAmount, string $countryCode = 'AE', string $taxClass = 'standard'): float
    {
        $rate = $this->rateFor($countryCode, $taxClass);

        return round($taxableAmount * $rate / 100, 2);
    }
}
