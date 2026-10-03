<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class Notify
{
    /** Send a notification to every staff user who can act on it. */
    public static function staff(Notification $notification): void
    {
        $recipients = User::whereHas('roles', fn ($q) => $q->whereIn('name', [
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
            UserRole::Manager->value,
            UserRole::InventoryManager->value,
        ]))->get();

        if ($recipients->isNotEmpty()) {
            NotificationFacade::send($recipients, $notification);
        }
    }
}
