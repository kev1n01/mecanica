<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make(),
            'Pagado' => Tab::make()
                ->badge(
                    SaleResource::getModel()::where('status', 'paid')->count(),
                )->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid')),
            'No pagado' => Tab::make()
                ->badge(
                    SaleResource::getModel()::where('status', 'unpaid')->count(),
                )->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'unpaid')),
        ];
    }
}
