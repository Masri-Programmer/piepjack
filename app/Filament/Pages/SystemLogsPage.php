<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;

class SystemLogsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $title = 'System Logs';

    protected static string $view = 'filament.pages.system-logs-page';

    public static function canAccess(): bool
    {
        $user = auth()->guard('staff')->user();

        return $user && (bool) $user->admin;
    }

    protected static ?string $slug = 'system-logs';

    public static function getNavigationGroup(): ?string
    {
        return 'Einstellungen';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cpu-chip';
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
}
