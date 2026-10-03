<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'products' => ['view', 'create', 'edit', 'delete'],
            'categories' => ['view', 'manage'],
            'brands' => ['view', 'manage'],
            'attributes' => ['view', 'manage'],
            'inventory' => ['view', 'adjust'],
            'orders' => ['view', 'update', 'delete'],
            'customers' => ['view', 'manage'],
            'reviews' => ['view', 'moderate'],
            'coupons' => ['view', 'manage'],
            'banners' => ['view', 'manage'],
            'pages' => ['view', 'manage'],
            'shipping' => ['view', 'manage'],
            'tax' => ['view', 'manage'],
            'newsletter' => ['view', 'manage'],
            'reports' => ['view'],
            'settings' => ['manage'],
            'users' => ['manage'],
            'activity' => ['view'],
        ];

        $permissionIds = [];
        foreach ($groups as $group => $actions) {
            foreach ($actions as $action) {
                $name = "{$group}.{$action}";
                $permission = Permission::updateOrCreate(
                    ['name' => $name],
                    ['group' => $group, 'label' => Str::headline($name)],
                );
                $permissionIds[$name] = $permission->id;
            }
        }

        $roles = [
            UserRole::SuperAdmin->value => ['label' => 'Super Admin', 'permissions' => array_keys($permissionIds)],
            UserRole::Admin->value => ['label' => 'Admin', 'permissions' => array_keys($permissionIds)],
            UserRole::Manager->value => ['label' => 'Manager', 'permissions' => [
                'products.view', 'products.create', 'products.edit',
                'categories.view', 'categories.manage', 'brands.view', 'brands.manage',
                'inventory.view', 'inventory.adjust', 'orders.view', 'orders.update',
                'customers.view', 'reviews.view', 'reviews.moderate', 'reports.view',
                'coupons.view', 'coupons.manage', 'banners.view', 'banners.manage', 'pages.view', 'pages.manage',
            ]],
            UserRole::SalesManager->value => ['label' => 'Sales Manager', 'permissions' => [
                'orders.view', 'orders.update', 'customers.view', 'customers.manage',
                'coupons.view', 'coupons.manage', 'reports.view', 'products.view',
            ]],
            UserRole::InventoryManager->value => ['label' => 'Inventory Manager', 'permissions' => [
                'products.view', 'products.edit', 'inventory.view', 'inventory.adjust',
                'orders.view', 'reports.view',
            ]],
            UserRole::Customer->value => ['label' => 'Customer', 'permissions' => []],
        ];

        foreach ($roles as $name => $config) {
            $role = Role::updateOrCreate(
                ['name' => $name],
                ['label' => $config['label']],
            );

            $ids = array_values(array_intersect_key(
                $permissionIds,
                array_flip($config['permissions'])
            ));

            $role->permissions()->sync($ids);
        }
    }
}
