<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'logo', 'description', 'website',
        'is_active', 'is_featured', 'position', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Brand $brand) {
            if (blank($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->orderBy('position')->orderBy('name');
    }

    public function logoUrl(): string
    {
        return media($this->logo)
            ?? 'https://placehold.co/200x80/ffffff/000066?text='.urlencode($this->name);
    }
}
