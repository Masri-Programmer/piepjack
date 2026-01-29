<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupSQLite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:sqlite';

    /**
     * The console command description.
     *
     * @var stringp
     */
    protected $description = 'Backup the SQLite database file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databasePath = database_path('piepjackclothing.sqlite');
        $backupPath = 'backup_folder/piepjackclothing_backup_' . now()->format('Y-m-d_H-i-s') . '.sqlite';

        if (! Storage::disk('private')->exists('backup_folder')) {
            Storage::disk('private')->makeDirectory('backup_folder');
        }
        if (Storage::disk('private')->put($backupPath, file_get_contents($databasePath))) {
            $this->info('SQLite backup created successfully');
        } else {
            $this->error('Failed to create SQLite backup.');
        }
    }
}
