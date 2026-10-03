<?php

namespace App\Models;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'description', 'type', 'value', 'scope', 'min_order_total',
        'max_discount', 'usage_limit', 'per_user_limit', 'used_count',
        'starts_at', 'expires_at', 'is_active', 'product_ids', 'category_ids',
    ];

    protected $casts = [
        'type' => DiscountType::class,
        'scope' => CouponScope::class,
        'value' => 'decimal:2',
        'min_order_total' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'product_ids' => 'array',
        'category_ids' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(fn (Coupon $c) => $c->code = strtoupper($c->code));
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isWithinSchedule(): bool
    {
        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function hasUsesLeft(): bool
    {
        return $this->usage_limit === null || $this->used_count < $this->usage_limit;
    }
}
