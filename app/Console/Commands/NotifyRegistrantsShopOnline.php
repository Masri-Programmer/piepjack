<?php

namespace App\Console\Commands;

use App\Mail\ShopIsOnlineMail;
use App\Models\LaunchRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyRegistrantsShopOnline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:notify-online {--force : Send even if already sent}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Send "Shop is Online" email to all launch registrants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = LaunchRegistration::query();

        if (!$this->option('force')) {
            $query->whereNull('sent_online_notification_at');
        }

        $registrants = $query->get();
        \Illuminate\Support\Facades\Log::info("NotifyRegistrantsShopOnline: Found " . $registrants->count() . " registrants.");

        if ($registrants->isEmpty()) {
            $this->info('No registrants found to notify.');
            return 0;
        }

        $this->info("Sending emails to {$registrants->count()} registrants...");

        $bar = $this->output->createProgressBar($registrants->count());
        $bar->start();

        foreach ($registrants as $registrant) {
            try {
                \Illuminate\Support\Facades\Log::info("NotifyRegistrantsShopOnline: Sending to " . $registrant->email);
                Mail::to($registrant->email)->send(new ShopIsOnlineMail($registrant));
                
                $registrant->update([
                    'sent_online_notification_at' => now(),
                ]);
                \Illuminate\Support\Facades\Log::info("NotifyRegistrantsShopOnline: Sent successfully to " . $registrant->email);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("NotifyRegistrantsShopOnline: Failed for " . $registrant->email . ": " . $e->getMessage());
                $this->error("\nFailed to send email to {$registrant->email}: {$e->getMessage()}");
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All notifications processed.');

        return 0;
    }
}
