<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Address;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@vanzapack.test'],
            [
                'name' => 'Store Owner',
                'phone' => '+971 52 399 3759',
                'password' => Hash::make('ChangeMe@12345'),
                'email_verified_at' => now(),
                'is_active' => true,
                'must_change_password' => false,
                'customer_group' => 'staff',
            ],
        );
        $superAdmin->assignRole(UserRole::SuperAdmin);

        $staff = [
            ['manager@vanzapack.test', 'Morgan Reyes', UserRole::Manager],
            ['sales@vanzapack.test', 'Priya Nair', UserRole::SalesManager],
            ['inventory@vanzapack.test', 'Sam Okoye', UserRole::InventoryManager],
        ];
        foreach ($staff as [$email, $name, $role]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('Password@123'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'customer_group' => 'staff',
                ],
            );
            $user->assignRole($role);
        }

        // Demo customers
        $demo = User::updateOrCreate(
            ['email' => 'customer@vanzapack.test'],
            [
                'name' => 'Layla Hassan',
                'phone' => '+971 50 111 2233',
                'password' => Hash::make('Password@123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );
        $demo->assignRole(UserRole::Customer);
        Wishlist::firstOrCreate(['user_id' => $demo->id]);
        Address::create([
            'user_id' => $demo->id,
            'label' => 'Cafe',
            'first_name' => 'Layla', 'last_name' => 'Hassan',
            'phone' => '+971 50 111 2233',
            'line1' => 'Shop 4, Marina Walk', 'line2' => 'Dubai Marina',
            'city' => 'Dubai', 'state' => 'Dubai', 'postal_code' => '00000', 'country' => 'AE',
            'is_default_shipping' => true, 'is_default_billing' => true,
        ]);

        User::factory()->count(15)->create()->each(function (User $user) {
            $user->assignRole(UserRole::Customer);
            Wishlist::firstOrCreate(['user_id' => $user->id]);
            Address::create([
                'user_id' => $user->id,
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name.' ')[1] ?: 'Doe',
                'phone' => $user->phone ?? '+971500000000',
                'line1' => fake()->streetAddress(),
                'city' => fake()->randomElement(['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman']),
                'state' => 'Dubai', 'postal_code' => fake()->postcode(), 'country' => 'AE',
                'is_default_shipping' => true, 'is_default_billing' => true,
            ]);
        });
    }
}
