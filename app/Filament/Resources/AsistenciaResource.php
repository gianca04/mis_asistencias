<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsistenciaResource\Pages;
use App\Filament\Resources\AsistenciaResource\RelationManagers;
use App\Models\Asistencia;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Seccion;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

use Filament\Resources\Concerns\Translatable;
use Filament\Tables\Filters\SelectFilter;

class AsistenciaResource extends Resource
{
    //use Translatable;
    protected static ?string $model = Asistencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('estudiante_id')
                    ->required()
                    ->label('Estudiante')
                    ->prefixIcon('heroicon-m-user')
                    ->searchable()
                    ->options(
                        Estudiante::query()
                            ->get()
                            ->mapWithKeys(fn($e) => [$e->id => $e->dni . ' ' . $e->full_name])
                            ->toArray()
                    )
                    ->reactive()
                    ->placeholder('Seleccionar un estudiante')
                    ->helperText('Selecciona el estudiante para cargar sus matrículas.'),

                Select::make('matricula_id')
                    ->required()
                    ->native(false)
                    ->label('Matrícula')
                    ->options(function (callable $get) {
                        $estudianteId = $get('estudiante_id');
                        if (!$estudianteId) {
                            return [];
                        }

                        return Matricula::whereHas('estudiantes', fn($q) => $q->where('estudiante_id', $estudianteId))
                            ->get()
                            ->mapWithKeys(fn($m) => [$m->id => $m->formatted_codigo_matricula])
                            ->toArray();
                    })
                    ->placeholder('Seleccionar matrícula')
                    ->disabled(fn(callable $get) => !$get('estudiante_id'))
                    ->helperText('Selecciona una matrícula asociada al estudiante seleccionado.'),
                Forms\Components\DatePicker::make('fecha')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('estado')
                    ->options([
                        'tardanza' => 'TARDANZA',
                        'falta' => 'FALTA',
                        'justificado' => 'JUSTIFICADO'
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\Textarea::make('comentario')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('estudiante.full_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('matricula.codigo_matricula')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha') // Etiqueta en español
                    ->date()
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-s-calendar') // Icono (opcional)
                    ->weight(4), // Peso de la columna

                Tables\Columns\TextColumn::make('matricula.regla.hora_entrada')
                    ->label('Hora') // Etiqueta en español
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->weight(5), // Peso de la columna


                Tables\Columns\TextColumn::make('estado'),
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
                // Filtrar por estado (TARDANZA, FALTA, JUSTIFICADO)
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'tardanza' => 'Tardanza',
                        'falta' => 'Falta',
                        'justificado' => 'Justificado',
                        'presente' => 'Presente'
                    ]),


                // Filtrar por fecha específica
                DateRangeFilter::make('fecha')
                    ->label('Fecha exacta')
                    ->startDate(Carbon::now())->endDate(Carbon::now()),



                // Filtrar por año escolar de la matrícula (usando relación)
                SelectFilter::make('anio_escolar')
                    ->label('Año Escolar')
                    ->options(function () {
                        return Matricula::query()
                            ->select('anio_escolar')
                            ->distinct()
                            ->orderBy('anio_escolar', 'desc')
                            ->pluck('anio_escolar', 'anio_escolar')
                            ->toArray();
                    })
                    ->query(function ($query, $data) {
                        if (!filled($data['value'])) return $query;

                        // Relación con estudiante -> matriculas -> año
                        return $query->whereHas('matricula', function ($q) use ($data) {
                            $q->where('anio_escolar', $data['value']);
                        });
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
                Tables\Actions\BulkAction::make('marcarComoFalto')
                    ->label('Marcar como Faltó')
                    ->icon('heroicon-o-x-circle')
                    ->action(fn($records) => $records->each->update([
                        'estado' => 'falta',
                        'fecha' => now(),
                    ])),

                Tables\Actions\BulkAction::make('marcarComoPresente')
                    ->label('Marcar como presente')
                    ->icon('heroicon-o-x-circle')
                    ->action(fn($records) => $records->each->update([
                        'estado' => 'presente',
                        'fecha' => now(),
                    ])),
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
            'index' => Pages\ListAsistencias::route('/'),
            'create' => Pages\CreateAsistencia::route('/create'),
            'edit' => Pages\EditAsistencia::route('/{record}/edit'),
        ];
    }
}
