<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'sku', 'barcode', 'name', 'price', 'sale_price',
        'cost_price', 'weight', 'image', 'is_active', 'position',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function currentPrice(): float
    {
        if ($this->sale_price !== null) {
            return (float) $this->sale_price;
        }
        if ($this->price !== null) {
            return (float) $this->price;
        }

        return $this->product->currentPrice();
    }

    public function label(): string
    {
        return $this->attributeValues->pluck('value')->implode(' / ')
            ?: ($this->name ?? $this->sku);
    }

    public function availableStock(): int
    {
        $inv = $this->inventory;

        return $inv ? max(0, $inv->quantity - $inv->reserved) : 0;
    }

    public function inStock(): bool
    {
        return $this->product->allow_backorder || $this->availableStock() > 0;
    }

    public function imageUrl(): string
    {
        return media($this->image) ?? $this->product->primaryImageUrl();
    }
}
