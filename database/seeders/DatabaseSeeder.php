<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            CatalogSeeder::class,
            LogisticsSeeder::class,
            CouponSeeder::class,
            CmsSeeder::class,
            ReviewSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
