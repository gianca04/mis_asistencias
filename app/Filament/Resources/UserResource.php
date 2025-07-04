<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Validation\Rules\Unique;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $modelLabel = 'Usuario';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'apellido', 'dni'];  // Cambia 'nombre' por 'name' si es el nombre correcto en la base de datos
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Nombres' => $record->nombre . ' ' . $record->apellido,
            'DNI' => $record->dni,
        ];
    }
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        // Optimiza la consulta, asegurando que solo cargue lo necesario
        return parent::getGlobalSearchEloquentQuery(); // Elimina la relación inexistente 'user'
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->description('Datos básicos del usuario.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->regex('/^[A-Za-z\s]+$/')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre')
                            ->prefixIcon('heroicon-s-user'),

                        Forms\Components\TextInput::make('apellido')
                            ->required()
                            ->regex('/^[a-zA-Z\s]+$/')
                            ->maxLength(255)
                            ->label('Apellido')
                            ->prefixIcon('heroicon-s-user-circle'),

                        Forms\Components\TextInput::make('dni')
                            ->required()
                            ->numeric()
                            ->length(8)
                            ->label('DNI')
                            ->prefixIcon('heroicon-s-identification'),
                    ])
                    ->columns(3), // Distribuir en 3 columnas para mejor presentación

                Forms\Components\Section::make('Credenciales')
                    ->description('Acceso del usuario al sistema.')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignorable: fn($record) => $record)
                            ->label('Correo Electrónico')
                            ->prefixIcon('heroicon-o-envelope'),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                            ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->label('Contraseña'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Permisos y Rol')
                    ->description('Configuración de roles y permisos.')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Roles')
                            ->helperText('Selecciona los roles que este usuario tendrá.'),
                    ]),

                Forms\Components\Section::make('Foto de Perfil')
                    ->schema([
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto de Perfil')
                            ->imageEditor()
                            ->image()
                            ->imageCropAspectRatio('1:1')
                            ->helperText('Sube una foto de perfil de formato adecuado.')
                            ->directory('uploads/users'),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellido')
                    ->label('Apellido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dni')
                    ->label('DNI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol asignado')
                    ->searchable()
                    ->badge(),

                Tables\Columns\ImageColumn::make('foto')
                    ->label('Fotografia')
                    ->searchable()
                    ->circular(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo electronico')
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
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Filtrar por Rol')
                    ->placeholder('Todos los roles'),
            ])
            ->actions([

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
