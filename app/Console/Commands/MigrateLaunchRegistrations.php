<?php

namespace App\Console\Commands;

use App\Models\LaunchRegistration;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Lunar\Models\Customer;

class MigrateLaunchRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:migrate-launch-registrations {--force : Overwrite existing users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate launch registrations to users and lunar customers';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $registrations = LaunchRegistration::all();

        if ($registrations->isEmpty()) {
            $this->info('No registrations found to migrate.');

            return self::SUCCESS;
        }

        $this->info("Found {$registrations->count()} registrations. Starting migration...");

        $bar = $this->output->createProgressBar($registrations->count());
        $bar->start();

        foreach ($registrations as $registration) {
            DB::transaction(function () use ($registration) {
                // Split name into first and last name
                $parts = explode(' ', trim($registration->name), 2);
                $firstName = $parts[0];
                $lastName = $parts[1] ?? '';

                // 1. Create or get User
                $user = User::where('email', $registration->email)->first();

                if (! $user) {
                    $user = User::create([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $registration->email,
                        'password' => Hash::make(Str::random(16)),
                        'active' => true,
                    ]);
                }

                // 2. Create or get Customer
                $customer = $user->latestCustomer();

                if (! $customer) {
                    $customer = Customer::create([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ]);

                    // Link them
                    $user->customers()->attach($customer);
                }
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Migration completed successfully.');

        return self::SUCCESS;
    }
}
