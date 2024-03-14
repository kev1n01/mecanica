<?php

namespace App\Filament\Resources\ModelVehicleResource\Pages;

use App\Filament\Resources\ModelVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModelVehicle extends EditRecord
{
    protected static string $resource = ModelVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
