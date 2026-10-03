<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'placement', 'title', 'subtitle', 'image', 'mobile_image',
        'cta_label', 'cta_url', 'is_active', 'position', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderBy('position');
    }

    public function scopePlacement(Builder $query, string $placement): Builder
    {
        return $query->where('placement', $placement);
    }

    public function imageUrl(): string
    {
        return media($this->image);
    }
}
