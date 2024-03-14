<?php

namespace App\Filament\Resources\TypeVehicleResource\Pages;

use App\Filament\Resources\TypeVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypeVehicles extends ListRecords
{
    protected static string $resource = TypeVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
