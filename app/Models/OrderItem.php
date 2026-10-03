<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_variant_id', 'name', 'sku',
        'variant_label', 'quantity', 'unit_price', 'line_total', 'is_reviewed',
    ];

    protected $casts = [
        'variant_label' => 'array',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_reviewed' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function variantText(): ?string
    {
        return $this->variant_label ? implode(' / ', $this->variant_label) : null;
    }
}
