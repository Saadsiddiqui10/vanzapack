<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id', 'product_id', 'product_variant_id', 'quantity', 'unit_price',
    ];

    protected $casts = ['unit_price' => 'decimal:2'];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function lineTotal(): float
    {
        return round((float) $this->unit_price * $this->quantity, 2);
    }

    public function purchasable(): Product|ProductVariant|null
    {
        return $this->variant ?: $this->product;
    }

    public function availableStock(): int
    {
        return $this->variant
            ? $this->variant->availableStock()
            : ($this->product?->availableStock() ?? 0);
    }
}
