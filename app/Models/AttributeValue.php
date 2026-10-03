<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value', 'slug', 'color_hex', 'position'];

    protected static function booted(): void
    {
        static::saving(function (AttributeValue $value) {
            if (blank($value->slug)) {
                $value->slug = Str::slug($value->value);
            }
        });
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'attribute_value_product');
    }
}
