<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = [
        'name', 'country', 'state', 'tax_class', 'rate', 'is_active', 'priority',
    ];

    protected $casts = [
        'rate' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
