<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = true;

    protected $fillable = ['group', 'key', 'value', 'type'];

    public function castedValue(): mixed
    {
        return match ($this->type) {
            'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'int' => (int) $this->value,
            'json' => json_decode($this->value ?? 'null', true),
            default => $this->value,
        };
    }
}
