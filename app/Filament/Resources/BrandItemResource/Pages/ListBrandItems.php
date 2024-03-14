<?php

namespace App\Filament\Resources\BrandItemResource\Pages;

use App\Filament\Resources\BrandItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrandItems extends ListRecords
{
    protected static string $resource = BrandItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
