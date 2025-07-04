<?php

namespace App\Filament\Resources\MatriculaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EstudiantesRelationManager extends RelationManager
{
    protected static string $relationship = 'estudiantes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([

                        // Campo 'nombre'
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ingresa el primer nombre del estudiante.')
                            ->placeholder('Ejemplo: Juan'),

                        // Campo 'apellido'
                        Forms\Components\TextInput::make('apellido')
                            ->label('Apellido')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ingresa el apellido del estudiante.')
                            ->placeholder('Ejemplo: Pérez'),

                        // Campo 'dni' con validación de longitud exacta y formato numérico
                        Forms\Components\TextInput::make('dni')
                            ->label('DNI')
                            ->required()
                            ->numeric()
                            ->length(8)
                            ->helperText('El DNI debe ser un número de 8 dígitos.')
                            ->placeholder('Ejemplo: 12345678'),

                        // Campo 'telefono' con validación de longitud exacta y formato numérico
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->length(9)
                            ->numeric()
                            ->helperText('El número de teléfono debe tener 9 dígitos (sin guiones ni espacios).')
                            ->placeholder('Ejemplo: 987654321'),

                        // Campo 'direccion'
                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->helperText('Ingresa la dirección completa del estudiante.')
                            ->placeholder('Ejemplo: Calle Ficticia 123'),

                        // Campo 'codigo_estudiante'
                        Forms\Components\TextInput::make('codigo_estudiante')
                            ->label('Código de Estudiante')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Este es el código único del estudiante.')
                            ->placeholder('Ejemplo: E12345'),

                        Forms\Components\Section::make('biometrico')
                            ->label('Biometricos')
                            ->relationship('biometrico')
                            ->columns(2)
                            ->schema([
                                Forms\Components\FileUpload::make('foto_perfil')
                                    ->label('Foto de Perfil')
                                    ->image()
                                    ->directory(directory: 'uploads/estudiantes')
                                    ->required()
                                    ->maxSize(1024) // Tamaño máximo de 1MB
                                    ->helperText('Sube una foto de perfil del estudiante.'),

                                Forms\Components\FileUpload::make('foto_frontal')
                                    ->label('Foto Frontal')
                                    ->image()
                                    ->directory(directory: 'uploads/estudiantes')
                                    ->required()
                                    ->maxSize(1024) // Tamaño máximo de 1MB
                                    ->helperText('Sube una foto frontal del estudiante.'),


                            ]),
                    ])
                    ->columns(2),  // Definir 2 columnas para mejorar el diseño
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('dni')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Nombre Completo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dni')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo_estudiante')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make() // <-- Permite seleccionar estudiantes existentes
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['nombre', 'apellido', 'dni']),
            ])
            ->actions([

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([


                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\DetachBulkAction::make(),
                    ExportBulkAction::make()

                ]),
            ]);
    }
}
