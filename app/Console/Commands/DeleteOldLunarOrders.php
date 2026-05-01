<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Order;
use Carbon\Carbon;

class DeleteOldLunarOrders extends Command
{
    /**
     * The name and signature of the console command.
     * We accept an optional --date parameter.
     *
     * @var string
     */
    protected $signature = 'lunar:delete-old-orders {--date= : The cut-off date (YYYY-MM-DD). Orders placed before this will be deleted.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely delete old Lunar orders and their related records from production.';

    /**
     * Execute the console command.
     */
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateInput = $this->option('date');

        if (!$dateInput) {
            $this->error('Please provide a date using --date=YYYY-MM-DD');
            return Command::FAILURE;
        }

        try {
            $cutoffDate = \Carbon\Carbon::createFromFormat('Y-m-d', $dateInput)->startOfDay();
        } catch (\Exception $e) {
            $this->error('Invalid date format. Please use YYYY-MM-DD.');
            return Command::FAILURE;
        }

        // Find orders placed before the cutoff date
        $orders = \Lunar\Models\Order::where('created_at', '<', $cutoffDate)->get();

        if ($orders->isEmpty()) {
            $this->info("No orders found before {$cutoffDate->toDateString()}.");
            return Command::SUCCESS;
        }

        $this->warn("You are about to permanently delete {$orders->count()} order(s) placed before {$cutoffDate->toDateString()}.");

        if (!$this->confirm('Are you sure you want to proceed? This cannot be undone.')) {
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
            $this->info("Successfully deleted {$orders->count()} order(s).");

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