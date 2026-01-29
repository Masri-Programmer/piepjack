<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found, skipping address seeding.');
            return;
        }

        foreach ($users as $user) {
            // Create one default address for each user
            Address::factory()->create([
                'user_id' => $user->id,
                'is_default_shipping' => true,
                'is_default_billing' => true,
            ]);

            // Optionally, create a second, non-default address for some users
            if (rand(0, 1)) {
                Address::factory()->create([
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
