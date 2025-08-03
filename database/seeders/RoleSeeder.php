<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an Admin role
        Role::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Administrator with full system access.']
        );

        // Create a Customer role
        Role::firstOrCreate(
            ['name' => 'Customer'],
            ['description' => 'A standard customer user.']
        );
    }
}
