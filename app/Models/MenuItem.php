<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['label', 'url', 'type', 'is_active', 'open_in_new_tab', 'position'];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position');
    }

    /** Resolve the href — supports absolute URLs and site-relative paths. */
    public function href(): string
    {
        $url = (string) $this->url;

        if ($url === '' || $url === '#') {
            return '#';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return url($url);
    }

    public function isMega(): bool
    {
        return $this->type === 'mega';
    }
}
