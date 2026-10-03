<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public static function log(string $action, string $description, ?Model $subject = null, array $properties = []): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'ip_address' => request()->ip(),
            'properties' => $properties ?: null,
        ]);
    }
}
