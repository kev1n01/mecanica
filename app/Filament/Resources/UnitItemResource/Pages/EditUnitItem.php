<?php

namespace App\Filament\Resources\UnitItemResource\Pages;

use App\Filament\Resources\UnitItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitItem extends EditRecord
{
    protected static string $resource = UnitItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
