<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. Create Roles ---
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Administrator with full system access.']
        );
        $userRole = Role::firstOrCreate(
            ['name' => 'Customer'],
            ['description' => 'A standard customer user.']
        );

        // --- 2. Create the specific Admin User ---
        $adminUser = User::firstOrCreate(
            ['email' => 'info@piepjack-clothing.de'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'active' => 1,
                'password' => Hash::make('asdQWE@34104758'),
                'email_verified_at' => now(),
            ]
        );

        if ($adminUser->roles()->where('role_id', $adminRole->id)->doesntExist()) {
            $adminUser->roles()->attach($adminRole);
        }

        User::factory(20)->create()->each(function ($user) use ($userRole) {
            $user->roles()->attach($userRole);
        });
    }
}
