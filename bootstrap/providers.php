<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\TelescopeServiceProvider;
use Lunar\Admin\LunarPanelProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    AdminPanelProvider::class,
    FortifyServiceProvider::class,
    RepositoryServiceProvider::class,
    TelescopeServiceProvider::class,
    LunarPanelProvider::class,
];
