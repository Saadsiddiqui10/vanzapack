<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = [
        'product_id', 'product_variant_id', 'quantity', 'reserved', 'low_stock_threshold',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class)->latest();
    }

    public function available(): int
    {
        return max(0, $this->quantity - $this->reserved);
    }

    public function isLow(): bool
    {
        return $this->available() <= $this->low_stock_threshold;
    }
}
