<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'sku', 'barcode',
        'short_description', 'description', 'specifications', 'shipping_info', 'return_info',
        'price', 'sale_price', 'cost_price', 'tax_class', 'weight',
        'has_variants', 'track_inventory', 'allow_backorder', 'status',
        'is_featured', 'is_new_arrival', 'is_best_seller', 'tags',
        'meta_title', 'meta_description', 'og_image', 'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'has_variants' => 'boolean',
        'track_inventory' => 'boolean',
        'allow_backorder' => 'boolean',
        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_best_seller' => 'boolean',
        'status' => ProductStatus::class,
        'tags' => 'array',
        'rating_avg' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (blank($product->slug)) {
                $product->slug = Str::slug($product->name).'-'.Str::lower(Str::random(5));
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Relationships ────────────────────────────────────────────────
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('position');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class)->whereNull('product_variant_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved')->latest();
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_value_product');
    }

    // ── Scopes ──────────────────────────────────────────────────────
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Active->value);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeNewArrivals(Builder $query): Builder
    {
        return $query->where('is_new_arrival', true);
    }

    public function scopeBestSellers(Builder $query): Builder
    {
        return $query->where('is_best_seller', true);
    }

    public function scopeInCategory(Builder $query, Category $category): Builder
    {
        return $query->whereIn('category_id', $category->descendantIds());
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $like = '%'.$term.'%';
            $q->where('name', 'like', $like)
                ->orWhere('sku', 'like', $like)
                ->orWhere('short_description', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $like))
                ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like));
        });
    }

    // ── Pricing / stock helpers ─────────────────────────────────────
    public function currentPrice(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price < (float) $this->price;
    }

    public function discountPercent(): int
    {
        if (! $this->isOnSale()) {
            return 0;
        }

        return (int) round(100 - ($this->currentPrice() / (float) $this->price * 100));
    }

    public function availableStock(): int
    {
        if (! $this->track_inventory) {
            return PHP_INT_MAX;
        }

        if ($this->has_variants) {
            return (int) $this->inventories->sum(fn ($i) => max(0, $i->quantity - $i->reserved));
        }

        $inv = $this->inventory;

        return $inv ? max(0, $inv->quantity - $inv->reserved) : 0;
    }

    public function inStock(): bool
    {
        return $this->allow_backorder || $this->availableStock() > 0;
    }

    public function primaryImageUrl(): string
    {
        $image = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        return $image
            ? $image->url()
            : 'https://placehold.co/600x600/f4f7ee/2c3d13?text='.urlencode(Str::limit($this->name, 20, ''));
    }

    public function url(): string
    {
        return route('product.show', $this->slug);
    }

    /** Recalculate cached rating aggregates from approved reviews. */
    public function refreshRating(): void
    {
        $stats = $this->reviews()
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as c, COALESCE(AVG(rating), 0) as a')
            ->first();

        $this->forceFill([
            'rating_count' => (int) $stats->c,
            'rating_avg' => round((float) $stats->a, 2),
        ])->saveQuietly();
    }
}
