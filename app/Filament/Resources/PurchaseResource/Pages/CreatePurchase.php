<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Models\Item;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['status'] === 'recibido') {
            foreach ($data['repeaters'] as $repeater) {
                $item = Item::find($repeater['item_id']);
                $item->stock += $repeater['quantity'];
                $item->save();
            }
        }
        return $data;
    }
}
