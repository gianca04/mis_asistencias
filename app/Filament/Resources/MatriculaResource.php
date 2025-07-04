<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstudianteMatriculasResource\RelationManagers\EstudianteResourceRelationManager;
use App\Filament\Resources\MatriculaResource\Pages;
use App\Filament\Resources\MatriculaResource\RelationManagers;
use App\Filament\Resources\MatriculaResource\RelationManagers\EstudiantesRelationManager;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Regla;
use App\Models\Role;
use App\Models\Seccion;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Carbon\Carbon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\RelationManagers\RelationManager;

class MatriculaResource extends Resource
{
    //use Translatable;
    protected static ?string $model = Matricula::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    // === FORMULARIO ===
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Sección 1: Detalles de Matrícula (4 columnas)
                Forms\Components\Section::make('Detalles de Matrícula')
                    ->columns(4)  // Se definen 4 columnas para un diseño más espacioso
                    ->schema([
                        Forms\Components\Select::make('grado_id')
                            ->label('Grado')
                            ->required()
                            ->searchable()
                            ->options(function (callable $get) {
                                return Grado::query()
                                    ->select('id', 'nombre')
                                    ->when($get('search'), function (Builder $query, $search) {
                                        $query->where('nombre', 'like', "%{$search}%");
                                    })
                                    ->get()
                                    ->pluck('nombre', 'id');
                            }),

                        Forms\Components\Select::make('seccion_id')
                            ->label('Sección')
                            ->required()
                            ->searchable()
                            ->options(function (callable $get) {
                                return Seccion::query()
                                    ->select('id', 'nombre')
                                    ->when($get('search'), function (Builder $query, $search) {
                                        $query->where('nombre', 'like', "%{$search}%");
                                    })
                                    ->get()
                                    ->pluck('nombre', 'id');
                            }),

                        Forms\Components\TextInput::make('anio_escolar')
                            ->label('Año Escolar')
                            ->required()
                            ->length(4)
                            ->numeric()
                            ->default(Carbon::now()->year)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                static::actualizarCodigoMatricula($set, $get);
                            }),

                        Forms\Components\TextInput::make('codigo_matricula')
                            ->label('Código de Matrícula')
                            ->maxLength(255)
                            ->readonly(),
                    ]),

                // Sección 2: Selección de Regla (1 columna)
                Forms\Components\Section::make('Regla')
                    ->description('Selecciona la regla que se aplicará a esta matrícula.')
                    ->schema([
                        Forms\Components\Select::make('regla_id')
                            ->label('Regla')
                            ->required()
                            ->options(function (callable $get) {
                                return Regla::query()
                                    ->select('id', 'name')
                                    ->when($get('search'), function (Builder $query, $search) {
                                        $query->where('name', 'like', "%{$search}%");
                                    })
                                    ->get()
                                    ->pluck('name', 'id');
                            })
                            ->helperText('Elige una regla aplicable para esta matrícula.')
                            ->searchable(),
                    ]),

                // Sección 3: Docente Asignado (1 columna)
                Forms\Components\Section::make('Docente Asignado')
                    ->description('Selecciona el docente que estará a cargo de la matrícula.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Docente Asignado')
                            ->required()
                            ->options(function (callable $get) {
                                $role = Role::where('name', 'Docente')->first();
                                if ($role) {
                                    $users = $role->users()
                                        ->select('users.id', 'users.name', 'users.apellido', 'users.dni')
                                        ->when($get('search'), function ($query, $search) {
                                            $query->where('users.name', 'like', "%{$search}%")
                                                ->orWhere('users.apellido', 'like', "%{$search}%")
                                                ->orWhere('users.dni', 'like', "%{$search}%");
                                        })
                                        ->get();

                                    return $users->mapWithKeys(function ($user) {
                                        return [
                                            $user->id => "{$user->name} {$user->apellido}",
                                        ];
                                    });
                                }
                                return [];
                            })
                            ->placeholder('Seleccionar docente')
                            ->helperText('Selecciona un docente de la lista o busca por nombre, apellido o DNI.')
                            ->searchable(),
                    ]),


            ]);
    }

    // Agrega este método estático en la misma clase
    protected static function actualizarCodigoMatricula(callable $set, callable $get)
    {
        $gradoId = $get('grado_id');
        $seccionId = $get('seccion_id');
        $anioEscolar = $get('anio_escolar') ?? now()->year;

        // Obtener nombre de la sección
        $seccionNombre = $seccionId ? Seccion::find($seccionId)?->nombre : '';

        if ($gradoId && $seccionNombre && $anioEscolar) {
            $codigo = $anioEscolar . $gradoId . strtoupper($seccionNombre);
            $set('codigo_matricula', $codigo);
        } else {
            $set('codigo_matricula', '');
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_matricula')
                    ->label('Código de Matrícula')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('grado.name')
                    ->label('Grado')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('seccion.nombre')
                    ->label('Sección')
                    ->badge()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('profesor.fullname')
                    ->label('Profesor Asignado')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('anio_escolar')
                    ->label('Año Escolar')
                    ->sortable()
                    ->alignCenter(),

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
                // Filtro por Año Escolar
                Tables\Filters\SelectFilter::make('anio_escolar')
                    ->label('Año Escolar')
                    ->options(function () {
                        return Matricula::query()
                            ->distinct()
                            ->pluck('anio_escolar', 'anio_escolar')
                            ->sort()
                            ->mapWithKeys(function ($anio) {
                                return [$anio => $anio];
                            })
                            ->toArray();
                    })
                    ->placeholder('Seleccionar Año Escolar'),
            ])
            ->actions([
                // Acción de editar


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
                // Acción para eliminar en bloque
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->icon('heroicon-o-trash')
                        ->tooltip('Eliminar matrículas seleccionadas'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            EstudiantesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatriculas::route('/'),
            'create' => Pages\CreateMatricula::route('/create'),
            'edit' => Pages\EditMatricula::route('/{record}/edit'),
        ];
    }
}
