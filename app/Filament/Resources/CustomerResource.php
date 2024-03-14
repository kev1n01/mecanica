<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $navigationLabel = 'Clientes';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->maxLength(60),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->wrap()
                    ->label('Nombre')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('num_doc')
                    ->label('N° documento')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Celular')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de actualización')
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
