<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Order;

class DeleteLunarOrdersByEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lunar:delete-spam-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely delete specific Lunar orders based on a hardcoded list of emails.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetEmails = [
            'example@gmail.com',
            'masri_mohamad@protonmail.com',
            'super-0021@hotmail.com',
            'mohmasri9753@gmail.com',
        ];

        $this->info('Searching for orders associated with the target emails...');

        // Find orders where the email matches the order address or the associated user account
        $orders = Order::whereHas('addresses', function ($query) use ($targetEmails) {
            $query->whereIn('contact_email', $targetEmails);
        })->orWhereHas('user', function ($query) use ($targetEmails) {
            $query->whereIn('email', $targetEmails);
        })->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found matching those emails.');
            return Command::SUCCESS;
        }

        $this->warn("Found {$orders->count()} order(s) matching the target emails.");

        if (!$this->confirm('Are you sure you want to permanently delete these orders? This cannot be undone.')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $bar = $this->output->createProgressBar($orders->count());
            $bar->start();

            foreach ($orders as $order) {
                // 1. Delete standard Eloquent relations
                $order->lines()->delete();
                $order->addresses()->delete();
                $order->transactions()->delete();

                // 2. Delete shipping zone records directly from the database
                DB::table('lunar_order_shipping_zone')
                    ->where('order_id', $order->id)
                    ->delete();

                // 3. Finally, delete the order itself
                $order->delete();

                $bar->advance();
            }

            DB::commit();

            $bar->finish();
            $this->newLine();
            $this->info("Successfully deleted {$orders->count()} targeted order(s).");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine();
            $this->error('An error occurred. All changes have been rolled back.');
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}