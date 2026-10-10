<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class Notify
{
    /**
     * The one inbox that receives every store email (new orders, low stock,
     * contact form, newsletter sign-ups). Editable in Admin → Settings.
     */
    public static function inbox(): ?string
    {
        return settings('notification_email') ?: settings('store_sales_email') ?: settings('store_email', config('store.email'));
    }

    /**
     * Staff alert: shown in the admin panel (bell) for every active staff user,
     * and emailed once to the store inbox — never to individual staff accounts.
     */
    public static function staff(Notification $notification): void
    {
        $recipients = User::query()->where('is_active', true)->whereHas('roles', fn ($q) => $q->whereIn('name', [
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
            UserRole::Manager->value,
            UserRole::InventoryManager->value,
        ]))->get();

        if ($recipients->isNotEmpty()) {
            NotificationFacade::send($recipients, $notification);
        }

        if ($inbox = self::inbox()) {
            NotificationFacade::route('mail', $inbox)->notify($notification);
        }
    }
}
