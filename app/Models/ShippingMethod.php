<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [
        'name', 'description', 'zone', 'country', 'city', 'price',
        'free_shipping_threshold', 'estimated_delivery', 'is_active', 'position',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position');
    }

    public function costFor(float $subtotal): float
    {
        if ($this->free_shipping_threshold !== null && $subtotal >= (float) $this->free_shipping_threshold) {
            return 0.0;
        }

        return (float) $this->price;
    }
}
