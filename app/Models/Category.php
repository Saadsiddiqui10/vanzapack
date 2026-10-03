<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id', 'name', 'slug', 'description', 'image', 'banner',
        'is_active', 'is_featured', 'position', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if (blank($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('position');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** IDs of this category and all descendants (for product filtering). */
    public function descendantIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->descendantIds());
        }

        return $ids;
    }

    public function imageUrl(): string
    {
        return media($this->image)
            ?? 'https://placehold.co/400x400/e6f0d9/2c3d13?text='.urlencode($this->name);
    }
}
