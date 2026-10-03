<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'user_id', 'email', 'phone', 'status', 'payment_status', 'payment_method',
        'subtotal', 'discount_total', 'shipping_total', 'tax_total', 'grand_total', 'currency',
        'coupon_code', 'shipping_method_id', 'shipping_method_name', 'tracking_number',
        'shipping_address', 'billing_address', 'customer_note', 'staff_note',
        'confirmed_at', 'shipped_at', 'delivered_at', 'cancelled_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'payment_method' => PaymentMethod::class,
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function totalItems(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [OrderStatus::Pending, OrderStatus::Confirmed], true);
    }
}
