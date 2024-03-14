<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Item;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Number;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Compras';

    protected static ?string $modelLabel = 'Compra';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Información de la compra')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Comprador')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->afterStateHydrated(function ($set) {
                                $set('user_id', auth()->user()->id);
                            })
                            ->relationship('user', 'name')
                            ->placeholder('Selecciona un usuario')
                            ->required(),
                        Forms\Components\Select::make('provider_id')
                            ->label('Proveedor')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->relationship('provider', 'name')
                            ->placeholder('Selecciona un proveedor')
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Método de pago')
                            ->native(false)
                            ->options([
                                'efectivo' => 'Efectivo',
                                'transferencia' => 'Transferencia',
                                'yape' => 'Yape',
                                'plin' => 'Plin',
                                'deposito' => 'Deposito',
                            ])
                            ->placeholder('Selecciona un método de pago')
                            ->required(),
                        Forms\Components\Select::make('type_cpe')
                            ->label('Tipo de comprobante')
                            ->native(false)
                            ->options([
                                'factura' => 'Factura',
                                'boleta' => 'Boleta',
                            ])
                            ->placeholder('Selecciona un tipo de comprobante')
                            ->required(),
                        Forms\Components\TextInput::make('nro_cpe')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Estado de compra')
                            ->native(false)
                            ->options([
                                'recibido' => 'Recibido',
                                'pendiente' => 'Pendiente',
                            ])
                            ->placeholder('Selecciona un estado de compra')
                            ->required(),

                        Forms\Components\DatePicker::make('date')
                            ->label('Fecha de compra')
                            ->native(false)
                            ->required(),
                        Forms\Components\Textarea::make('observation')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Fieldset::make('Detalles de la compra')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Productos')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('item_id')
                                    ->label('Item')
                                    ->relationship('item', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(4)
                                    ->reactive()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->afterStateUpdated(fn ($state, Set $set) =>  $set('unit_amount', Item::find($state)?->sale_price ?? 0))
                                    ->afterStateUpdated(fn ($state, Set $set) =>  $set('price_total', Item::find($state)?->sale_price ?? 0))
                                    ->placeholder('Selecciona un item'),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
                                    ->dehydrated()
                                    ->minValue(1)
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) =>  $set('price_total', $state * $get('unit_amount')))
                                    ->columnSpan(2)
                                    ->required(),

                                Forms\Components\TextInput::make('unit_amount')
                                    ->label('Precio unitario')
                                    ->numeric()
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) =>  $set('price_total', $state * $get('quantity')))
                                    ->dehydrated()
                                    ->reactive()
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('price_total')
                                    ->label('Precio total')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->dehydrated()
                                    ->disabled()
                                    ->columnSpan(3),

                                Forms\Components\Hidden::make('price_total')
                                    ->default(0),
                            ])
                            ->afterStateUpdated(fn ($state, Set $set) =>  $set('repeaters', $state))
                            ->reorderable(true)
                            ->collapsible()
                            ->reorderableWithButtons()
                            ->columns(12),

                        Forms\Components\Placeholder::make('grand_total')
                            ->label('Total')
                            ->content(function (Get $get, Set $set) {
                                $total = 0;

                                if (!$repeaters = $get('items')) return Number::currency($total, 'S/.');

                                foreach ($repeaters as $key => $repeater) {
                                    $set("items.{$key}.price_total", $get("items.{$key}.unit_amount") * $get("items.{$key}.quantity"));
                                    // $total += $get("items.{$key}.price_total") * $get("items.{$key}.quantity");
                                    $total += $get("items.{$key}.price_total");
                                }
                                // print_r($get('items'));


                                $set('total', $total);
                                return Number::currency($total, 'S/.');
                            })
                            ->disabled(),

                        Forms\Components\Hidden::make('total')
                            ->default(0),
                        Forms\Components\Hidden::make('repeaters')
                            ->default([]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_cpe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_cpe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
