<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case Manager = 'manager';
    case SalesManager = 'sales-manager';
    case InventoryManager = 'inventory-manager';
    case Customer = 'customer';

    public function label(): string
    {
        return ucwords(str_replace('-', ' ', $this->value));
    }

    /** Roles allowed to reach the /admin area. */
    public static function staff(): array
    {
        return [
            self::SuperAdmin->value,
            self::Admin->value,
            self::Manager->value,
            self::SalesManager->value,
            self::InventoryManager->value,
        ];
    }
}
