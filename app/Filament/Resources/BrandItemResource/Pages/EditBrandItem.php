<?php

namespace App\Filament\Resources\BrandItemResource\Pages;

use App\Filament\Resources\BrandItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrandItem extends EditRecord
{
    protected static string $resource = BrandItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
