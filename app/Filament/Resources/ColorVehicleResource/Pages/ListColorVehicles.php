<?php

namespace App\Filament\Resources\ColorVehicleResource\Pages;

use App\Filament\Resources\ColorVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListColorVehicles extends ListRecords
{
    protected static string $resource = ColorVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
