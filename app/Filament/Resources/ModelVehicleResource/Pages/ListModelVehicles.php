<?php

namespace App\Filament\Resources\ModelVehicleResource\Pages;

use App\Filament\Resources\ModelVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModelVehicles extends ListRecords
{
    protected static string $resource = ModelVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
