<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'first_name', 'last_name', 'phone',
        'line1', 'line2', 'city', 'state', 'postal_code', 'country',
        'is_default_shipping', 'is_default_billing',
    ];

    protected $casts = [
        'is_default_shipping' => 'boolean',
        'is_default_billing' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function toSnapshot(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'line1' => $this->line1,
            'line2' => $this->line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
        ];
    }
}
