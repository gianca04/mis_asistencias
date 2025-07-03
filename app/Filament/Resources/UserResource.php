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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $modelLabel = 'Usuario';

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
                            ->regex('/^[A-Za-z\s]+$/')
                            ->alpha()
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dni')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('foto')
                    ->searchable()
                    ->circular(),
                Tables\Columns\TextColumn::make('email')
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
