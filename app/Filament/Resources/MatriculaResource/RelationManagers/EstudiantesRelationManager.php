<?php

namespace App\Filament\Resources\MatriculaResource\RelationManagers;

use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Seccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Estudiante;
use App\Imports\EstudiantesImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

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
                // Filtrar por año escolar de la matrícula (usando relación)
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make() // <-- Permite seleccionar estudiantes existentes
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['nombre', 'apellido', 'dni']),

                // Acción para registro masivo de estudiantes existentes
                Action::make('attachMultiple')
                    ->label('Registrar Múltiples Estudiantes')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->form([
                        Forms\Components\CheckboxList::make('estudiantes')
                            ->label('Seleccionar Estudiantes')
                            ->options(function () {
                                return Estudiante::whereDoesntHave('matriculas', function ($query) {
                                    $query->where('matricula_id', $this->ownerRecord->id);
                                })
                                    ->get()
                                    ->mapWithKeys(function ($estudiante) {
                                        return [
                                            $estudiante->id => "{$estudiante->nombre} {$estudiante->apellido} - DNI: {$estudiante->dni} - Código: {$estudiante->codigo_estudiante}"
                                        ];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->columns(1)
                            ->helperText('Selecciona los estudiantes que deseas registrar en esta matrícula'),

                        Forms\Components\Placeholder::make('info')
                            ->content(function () {
                                $count = Estudiante::whereDoesntHave('matriculas', function ($query) {
                                    $query->where('matricula_id', $this->ownerRecord->id);
                                })->count();
                                return "Hay {$count} estudiantes disponibles para registrar en esta matrícula.";
                            }),
                    ])
                    ->modalWidth('2xl')
                    ->action(function (array $data) {
                        $estudiantesIds = $data['estudiantes'];
                        $matricula = $this->ownerRecord;

                        // Verificar que no estén ya registrados
                        $yaRegistrados = $matricula->estudiantes()
                            ->whereIn('estudiante_id', $estudiantesIds)
                            ->pluck('estudiante_id')
                            ->toArray();

                        $nuevosEstudiantes = array_diff($estudiantesIds, $yaRegistrados);

                        if (!empty($nuevosEstudiantes)) {
                            $matricula->estudiantes()->attach($nuevosEstudiantes);

                            Notification::make()
                                ->title('Estudiantes registrados exitosamente')
                                ->body(count($nuevosEstudiantes) . ' estudiantes fueron registrados en la matrícula.')
                                ->success()
                                ->send();
                        }

                        if (!empty($yaRegistrados)) {
                            Notification::make()
                                ->title('Algunos estudiantes ya estaban registrados')
                                ->body(count($yaRegistrados) . ' estudiantes ya estaban registrados en esta matrícula.')
                                ->warning()
                                ->send();
                        }
                    }),

                // Acción para importar desde Excel/CSV
                Action::make('importStudents')
                    ->label('Importar desde Excel')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('info')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Archivo Excel/CSV')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->required()
                            ->helperText('Sube un archivo Excel (.xlsx, .xls) o CSV con los datos de los estudiantes. Columnas requeridas: nombre, apellido, dni, codigo_estudiante'),

                        Forms\Components\Placeholder::make('template_info')
                            ->label('Formato del archivo')
                            ->content('El archivo debe contener las siguientes columnas: nombre, apellido, dni, telefono (opcional), direccion (opcional), codigo_estudiante'),
                    ])
                    ->action(function (array $data) {
                        $this->importEstudiantesFromFile($data['file']);
                    }),

                // Acción para descargar plantilla
                Action::make('downloadTemplate')
                    ->label('Descargar Plantilla')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function () {
                        return $this->downloadTemplate();
                    }),
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

    /**
     * Importar estudiantes desde archivo Excel/CSV
     */
    protected function importEstudiantesFromFile(string $filePath): void
    {
        try {
            $path = Storage::disk('public')->path($filePath);

            $estudiantesAsociados = 0;
            $errores = [];

            // Importar usando la clase personalizada
            try {
                $import = new EstudiantesImport();
                Excel::import($import, $path);

                // Usar los estudiantes importados de la clase
                $estudiantesImportados = $import->importedStudents;

                // Asociar estudiantes a la matrícula si no están ya asociados
                foreach ($estudiantesImportados as $estudiante) {
                    if (!$this->ownerRecord->estudiantes()->where('estudiante_id', $estudiante->id)->exists()) {
                        $this->ownerRecord->estudiantes()->attach($estudiante->id);
                        $estudiantesAsociados++;
                    }
                }

                Notification::make()
                    ->title('Importación completada exitosamente')
                    ->body("Se importaron " . count($estudiantesImportados) . " estudiantes. {$estudiantesAsociados} fueron asociados a esta matrícula.")
                    ->success()
                    ->send();
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                foreach ($failures as $failure) {
                    $errores[] = "Fila {$failure->row()}: " . implode(', ', $failure->errors());
                }

                Notification::make()
                    ->title('Errores en la validación')
                    ->body(implode("\n", array_slice($errores, 0, 5)) . (count($errores) > 5 ? "\n...y " . (count($errores) - 5) . " errores más" : ""))
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al procesar el archivo')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            // Limpiar archivo temporal
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }
    }

    /**
     * Descargar plantilla de Excel para importar estudiantes
     */
    protected function downloadTemplate()
    {
        $headers = [
            'nombre',
            'apellido',
            'dni',
            'telefono',
            'direccion',
            'codigo_estudiante'
        ];

        $sampleData = [
            [
                'Juan',
                'Pérez',
                '12345678',
                '987654321',
                'Av. Principal 123',
                'EST001'
            ],
            [
                'María',
                'García',
                '87654321',
                '123456789',
                'Calle Secundaria 456',
                'EST002'
            ]
        ];

        $filename = 'plantilla_estudiantes.xlsx';

        return Excel::download(new class($headers, $sampleData) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $headers;
            protected $data;

            public function __construct(array $headers, array $data)
            {
                $this->headers = $headers;
                $this->data = $data;
            }

            public function array(): array
            {
                return array_merge([$this->headers], $this->data);
            }
        }, $filename);
    }
}
