<?php

namespace App\Filament\Resources\BrandVehicleResource\Pages;

use App\Filament\Resources\BrandVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrandVehicle extends EditRecord
{
    protected static string $resource = BrandVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
