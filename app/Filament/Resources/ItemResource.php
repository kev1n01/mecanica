<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Items';

    protected static ?string $modelLabel = 'Item';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([

                // Tabs::make('Tabs')
                //     ->tabs([
                //         Tabs\Tab::make('Tipo servicio')
                //             ->schema([

                //             ]),
                //         Tabs\Tab::make('Tipo producto')
                //             ->schema([
                //                 // ...
                //             ]),
                //     ]),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('Codigo')
                    ->afterStateHydrated(function ($set) {
                        $code = random_int(10001, 99999);
                        $set('code', $code);
                    })
                    ->numeric()
                    ->length(5)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('stock')
                    ->label('Stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('sale_price')
                    ->label('Precio de venta')
                    ->prefix('S/')
                    ->required()
                    ->default(0)
                    ->numeric(),
                Forms\Components\TextInput::make('purchase_price')
                    ->label('Precio de compra')
                    ->prefix('S/')
                    ->required()
                    ->default(0)
                    ->numeric(),
                Forms\Components\Select::make('unit_item_id')
                    ->label('Unidad')
                    ->searchable()
                    ->preload()
                    ->relationship('unit', 'name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('short_name')
                            ->required()
                            ->required(),
                    ]),
                Forms\Components\Select::make('brand_item_id')
                    ->label('Marca')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                    ])
                    ->searchable()
                    ->preload()
                    ->relationship('brand', 'name'),
                Forms\Components\Select::make('category_item_id')
                    ->label('Categoria')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                    ])
                    ->relationship('category', 'name'),
                Forms\Components\Select::make('type')
                    ->label('Tipo de item')
                    ->options([
                        'product' => 'Producto',
                        'service' => 'Servicio',
                    ])
                    ->searchable()
                    ->required()
                    ->default('product')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unidad')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marca')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Codigo')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de item')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Precio venta')
                    ->numeric()
                    ->toggleable()
                    ->money('PEN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Precio compra')
                    ->numeric()
                    ->toggleable()
                    ->money('PEN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creacion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de actualizacion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
