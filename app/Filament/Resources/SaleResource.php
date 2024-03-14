<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Item;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Ventas';

    protected static ?string $modelLabel = 'Ventas';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Información de la venta')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Cliente')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->relationship('customer', 'name')
                            ->placeholder('Selecciona un cliente')
                            ->required(),
                        Forms\Components\Select::make('payment_type')
                            ->label('Tipo de pago')
                            ->native(false)
                            ->options([
                                'contado' => 'Contado',
                                'credito' => 'Credito',
                            ])
                            ->placeholder('Selecciona un tipo de pago')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Estado de pago')
                            ->native(false)
                            ->options([
                                'pagado' => 'Pagado',
                                'no pagado' => 'No pagado',
                            ])
                            ->placeholder('Selecciona un estado de pago')
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label('Tipo de venta')
                            ->native(false)
                            ->options([
                                'vehicular' => 'Vehicular',
                                'comercial' => 'Comercial',
                            ])
                            ->placeholder('Selecciona un tipo de venta')
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
                        Forms\Components\DatePicker::make('date')
                            ->native(false),
                        Forms\Components\Textarea::make('observation')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Fieldset::make('Detalles de la venta')
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

                        Forms\Components\TextInput::make('cash_paid')
                            ->label('Dinero recibido')
                            ->default(0)
                            ->minValue(0)
                            ->reactive()
                            ->live(debounce: 10),

                        Forms\Components\Placeholder::make('turned')
                            ->label('Vuelto')
                            ->content(function (Get $get, Set $set) {
                                if ($get('cash_paid') === null) {
                                    $set('cash_paid', 0);
                                };
                                $turned = floatval($get('cash_paid')) -  floatval($get('total'));
                                return Number::currency($turned, 'S/.');
                            })
                            ->disabled(),

                        Forms\Components\Hidden::make('total')
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->wrap(true)
                    ->size('xs')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_type')
                    ->label('Tipo de pago')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método de pago')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->prefix('S/ ')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cash_paid')
                    ->label('Dinero recibido')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Estado')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                ]),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
