<?php

namespace App\Filament\Resources\ColorVehicleResource\Pages;

use App\Filament\Resources\ColorVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditColorVehicle extends EditRecord
{
    protected static string $resource = ColorVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
