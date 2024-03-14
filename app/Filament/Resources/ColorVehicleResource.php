<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColorVehicleResource\Pages;
use App\Filament\Resources\ColorVehicleResource\RelationManagers;
use App\Filament\Resources\ColorVehicleResource\RelationManagers\VehiclesRelationManager;
use App\Models\ColorVehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ColorVehicleResource extends Resource
{
    protected static ?string $model = ColorVehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Colores';

    protected static ?string $modelLabel = 'Colore';

    protected static ?string $navigationParentItem = 'Vehiculos';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
            VehiclesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListColorVehicles::route('/'),
            'create' => Pages\CreateColorVehicle::route('/create'),
            'edit' => Pages\EditColorVehicle::route('/{record}/edit'),
        ];
    }
}
