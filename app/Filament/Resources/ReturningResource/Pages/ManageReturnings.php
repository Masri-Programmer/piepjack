<?php

namespace App\Filament\Resources\ReturningResource\Pages;

use App\Filament\Resources\ReturningResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageReturnings extends ManageRecords
{
    protected static string $resource = ReturningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
