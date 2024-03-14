<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Vehiculos';

    protected static ?string $modelLabel = 'Vehiculo';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles del vehiculo')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Cliente')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('num_doc')
                                    ->unique(ignoreRecord: true)
                                    ->label('N° de documento')
                                    ->required()
                                    ->maxLength(11)
                                    ->minLength(8)
                                    ->suffixAction(
                                        fn ($state, $livewire, $set) => Action::make('search')
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->action(function () use ($state, $livewire, $set) {
                                                $livewire->validateOnly('data.num_doc');
                                                if (blank($state)) {
                                                    Notification::make()
                                                        ->title('El N° de documento no puede estar vacío.')
                                                        ->danger()
                                                        ->send();
                                                    return;
                                                }

                                                if (strlen($state) === 8) {
                                                    $response = Http::get(
                                                        'https://dniruc.apisperu.com/api/v1/dni/' . $state . '?token=' . env('APIPERU_TOKEN_DOC')
                                                    )->throw()->json();

                                                    if (!$response['success']) {
                                                        Notification::make()
                                                            ->title($response['message'])
                                                            ->warning()
                                                            ->send();
                                                    }

                                                    if ($response['success']) {
                                                        Notification::make()
                                                            ->title('Datos del cliente encontrados.')
                                                            ->success()
                                                            ->send();
                                                        $set('name', $response['nombres'] . ' ' . $response['apellidoPaterno'] . ' ' . $response['apellidoMaterno'] ?? null);
                                                    }
                                                }

                                                if (strlen($state) === 11) {
                                                    $response = Http::get(
                                                        'https://dniruc.apisperu.com/api/v1/ruc/' . $state . '?token=' . env('APIPERU_TOKEN_DOC')
                                                    )->throw()->json();

                                                    if (count($response) <= 2) {
                                                        Notification::make()
                                                            ->title($response['message'] . ' o el número de RUC no se encuentra activo.')
                                                            ->warning()
                                                            ->send();
                                                    }

                                                    if (count($response) > 3) {
                                                        Notification::make()
                                                            ->title('Datos del cliente encontrados.')
                                                            ->success()
                                                            ->send();
                                                        $set('name', $response['razonSocial'] ?? null);
                                                        $set('address', $response['direccion'] ?? null);
                                                    }
                                                }
                                            })
                                    ),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('address')
                                    ->label('Dirección')
                                    ->columnSpanFull()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('email')
                                    ->label('Correo electrónico')
                                    ->unique(ignoreRecord: true)
                                    ->email()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Celular')
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('999999999')
                                    ->maxLength(9),
                            ])
                            ->relationship('customer', 'name'),
                        Forms\Components\Select::make('type_vehicle_id')
                            ->label('Tipo')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ])
                            ->relationship('type', 'name'),
                        Forms\Components\Select::make('brand_vehicle_id')
                            ->label('Marca')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ])
                            ->relationship('brand', 'name'),
                        Forms\Components\Select::make('model_vehicle_id')
                            ->label('Modelo')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ])
                            ->relationship('model', 'name'),
                        Forms\Components\Select::make('color_vehicle_id')
                            ->label('Color')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ])
                            ->relationship('color', 'name'),
                        Forms\Components\TextInput::make('plate')
                            ->label('Placa')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(7)
                            ->placeholder('XXX-XXX'),
                        Forms\Components\TextInput::make('year')
                            ->label('Año')
                            ->numeric()
                            ->maxLength(4),
                        Forms\Components\TextInput::make('odo')
                            ->label('Odometro')
                            ->numeric(),
                        Forms\Components\MarkdownEditor::make('note')
                            ->label('Nota')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(8)
                    ->columns(2),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Imagenes del vehiculo')
                        ->collapsible()
                        ->schema([
                            Forms\Components\FileUpload::make('images')
                                ->label('Imagenes')
                                ->maxFiles(4)
                                ->reorderable()
                                ->image()
                                ->imageEditor()
                                ->directory('vehicles')
                                ->openable()
                                ->multiple(),
                        ])
                ])
                    ->columnSpan(4),

            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marca')
                    ->sortable(),
                Tables\Columns\TextColumn::make('model.name')
                    ->label('Modelo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('color.name')
                    ->label('Color')
                    ->sortable(),
                Tables\Columns\TextColumn::make('plate')
                    ->label('Placa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->searchable(),
                Tables\Columns\TextColumn::make('odo')
                    ->label('ODO')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('images')
                    ->label('Imagenes')
                    ->circular()
                    ->limit(2)
                    ->stacked(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Nota')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
