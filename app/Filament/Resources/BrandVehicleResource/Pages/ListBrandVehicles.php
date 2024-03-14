<?php

namespace App\Filament\Resources\BrandVehicleResource\Pages;

use App\Filament\Resources\BrandVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrandVehicles extends ListRecords
{
    protected static string $resource = BrandVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
