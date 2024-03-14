<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make()
                ->badge(
                    ItemResource::getModel()::count(),
                ),
            'Productos' => Tab::make()
                ->badge(
                    ItemResource::getModel()::where('type', 'product')->count(),
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'product')),
            'Servicios' => Tab::make()
                ->badge(
                    ItemResource::getModel()::where('type', 'service')->count(),
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'service')),
        ];
    }
}
