<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'user_id', 'order_item_id', 'rating', 'title',
        'comment', 'status', 'is_verified_purchase',
    ];

    protected $casts = [
        'status' => ReviewStatus::class,
        'is_verified_purchase' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn (Review $r) => $r->product?->refreshRating());
        static::deleted(fn (Review $r) => $r->product?->refreshRating());
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Approved->value);
    }
}
