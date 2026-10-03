<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'inventory_id', 'type', 'quantity', 'balance_after',
        'reference_type', 'reference_id', 'user_id', 'note',
    ];

    protected $casts = ['type' => StockMovementType::class];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
