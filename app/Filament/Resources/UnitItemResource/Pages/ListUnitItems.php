<?php

namespace App\Filament\Resources\UnitItemResource\Pages;

use App\Filament\Resources\UnitItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitItems extends ListRecords
{
    protected static string $resource = UnitItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
