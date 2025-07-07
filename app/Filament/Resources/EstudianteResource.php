<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstudianteResource\Pages;
use App\Filament\Resources\EstudianteResource\RelationManagers;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Seccion;
use App\Models\Matricula;
use Illuminate\Support\Collection;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class EstudianteResource extends Resource
{
    //use Translatable;

    protected static ?string $model = Estudiante::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Sección de Información Personal
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

                                Forms\Components\FileUpload::make('foto_frontal')
                                    ->label('Foto Frontal')
                                    ->image()
                                    ->directory(directory: 'uploads/estudiantes')

                                    ->maxSize(3072) // Tamaño máximo de 3MB
                                    ->helperText('Sube una foto frontal del estudiante.'),

                            ]),
                    ])
                    ->columns(2),  // Definir 2 columnas para mejorar el diseño
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dni')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo_estudiante')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
            ])
            ->filters([
                //
                Tables\Filters\SelectFilter::make('anio_escolar')
                    ->label('Año de Matrícula')
                    ->options(function () {
                        return Matricula::query()
                            ->select('anio_escolar')
                            ->distinct()
                            ->orderBy('anio_escolar', 'desc')
                            ->pluck('anio_escolar', 'anio_escolar')
                            ->toArray();
                    })
                    ->query(function ($query, $data) {
                        if (filled($data['value'])) {
                            $query->whereHas('matriculas', function ($q) use ($data) {
                                $q->where('anio_escolar', $data['value']);
                            });
                        }
                    }),
                Tables\Filters\SelectFilter::make('grado_id')
                    ->label('Grado')
                    ->options(function () {
                        return Grado::orderBy('nombre')->pluck('nombre', 'id')->toArray();
                    })
                    ->query(function ($query, $data) {
                        if (!filled($data['value'])) {
                            return $query; // No aplicar filtro si no hay valor seleccionado
                        }

                        return $query->whereHas('matriculas', function ($q) use ($data) {
                            $q->where('grado_id', $data['value']);
                        });
                    }),

                Tables\Filters\SelectFilter::make('seccion_id')
                    ->label('Sección')
                    ->options(function () {
                        return Seccion::orderBy('nombre')->pluck('nombre', 'id')->toArray();
                    })
                    ->query(function ($query, $data) {
                        if (!filled($data['value'])) {
                            return $query; // No aplicar filtro si no hay valor seleccionado
                        }

                        return $query->whereHas('matriculas', function ($q) use ($data) {
                            $q->where('seccion_id', $data['value']);
                        });
                    }),



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
                    ExportBulkAction::make()
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
            'index' => Pages\ListEstudiantes::route('/'),
            'create' => Pages\CreateEstudiante::route('/create'),
            'edit' => Pages\EditEstudiante::route('/{record}/edit'),
        ];
    }
}
