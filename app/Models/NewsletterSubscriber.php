<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'is_confirmed', 'unsubscribed_at'];

    protected $casts = [
        'is_confirmed' => 'boolean',
        'unsubscribed_at' => 'datetime',
    ];
}
