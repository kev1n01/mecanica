<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModelVehicleResource\Pages;
use App\Filament\Resources\ModelVehicleResource\RelationManagers;
use App\Filament\Resources\ModelVehicleResource\RelationManagers\VehiclesRelationManager;
use App\Models\ModelVehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModelVehicleResource extends Resource
{
    protected static ?string $model = ModelVehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Modelos';

    protected static ?string $modelLabel = 'Modelo';

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
            'index' => Pages\ListModelVehicles::route('/'),
            'create' => Pages\CreateModelVehicle::route('/create'),
            'edit' => Pages\EditModelVehicle::route('/{record}/edit'),
        ];
    }
}