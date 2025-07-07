<?php

namespace App\Filament\Resources\AsistenciaResource\Pages;

use App\Filament\Resources\AsistenciaResource;
use App\Models\Asistencia;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Seccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class RegistroMasivo extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = AsistenciaResource::class;

    protected static string $view = 'filament.resources.asistencia-resource.pages.registro-masivo';

    protected static ?string $title = 'Registro Masivo de Asistencias';

    protected static ?string $navigationLabel = 'Registro Masivo';

    public ?array $data = [];
    public ?int $matriculaId = null;
    public ?string $fecha = null;
    public array $estudiantes = [];

    public function mount(): void
    {
        $this->estudiantes = [];
        $this->fecha = now()->format('Y-m-d');
        $this->data = [
            'matricula_id' => null,
            'fecha' => $this->fecha,
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Seleccionar Salón')
                    ->schema([
                        Forms\Components\Select::make('matricula_id')
                            ->label('Matrícula (Salón)')
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return Matricula::with(['grado', 'seccion'])
                                    ->get()
                                    ->mapWithKeys(function ($matricula) {
                                        $label = $matricula->grado->nombre . ' - ' .
                                               $matricula->seccion->nombre . ' (' .
                                               $matricula->anio_escolar . ')';
                                        return [$matricula->id => $label];
                                    })
                                    ->toArray();
                            })
                            ->placeholder('Selecciona un salón')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->matriculaId = $state;
                                $this->cargarEstudiantes();
                            }),

                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha')
                            ->required()
                            ->default(now())
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->fecha = $state;
                                $this->cargarEstudiantes();
                            }),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function cargarEstudiantes(): void
    {
        if (!$this->matriculaId || !$this->fecha) {
            $this->estudiantes = [];
            return;
        }

        $matricula = Matricula::with('estudiantes')->find($this->matriculaId);

        if (!$matricula || $matricula->estudiantes->isEmpty()) {
            $this->estudiantes = [];
            return;
        }

        $this->estudiantes = $matricula->estudiantes->map(function ($estudiante) {
            // Verificar si ya tiene asistencia registrada para esta fecha
            $asistenciaExistente = Asistencia::where('estudiante_id', $estudiante->id)
                ->where('matricula_id', $this->matriculaId)
                ->whereDate('fecha', $this->fecha)
                ->first();

            return [
                'id' => $estudiante->id,
                'dni' => $estudiante->dni,
                'nombre_completo' => $estudiante->full_name,
                'estado' => $asistenciaExistente ? $asistenciaExistente->estado : 'presente',
                'comentario' => $asistenciaExistente ? $asistenciaExistente->comentario : '',
                'ya_registrado' => $asistenciaExistente ? true : false,
                'asistencia_id' => $asistenciaExistente ? $asistenciaExistente->id : null,
            ];
        })->toArray();
    }

    public function marcarTodosComoPresentes(): void
    {
        foreach ($this->estudiantes as $index => $estudiante) {
            $this->estudiantes[$index]['estado'] = 'presente';
        }
    }

    public function guardarAsistencias(): void
    {
        if (!$this->matriculaId || !$this->fecha) {
            Notification::make()
                ->title('Error')
                ->body('Debes seleccionar una matrícula y fecha.')
                ->danger()
                ->send();
            return;
        }

        if (empty($this->estudiantes)) {
            Notification::make()
                ->title('Error')
                ->body('No hay estudiantes para procesar.')
                ->danger()
                ->send();
            return;
        }

        $registrosCreados = 0;
        $registrosActualizados = 0;

        foreach ($this->estudiantes as $estudianteData) {
            if ($estudianteData['ya_registrado'] && $estudianteData['asistencia_id']) {
                // Actualizar registro existente
                Asistencia::where('id', $estudianteData['asistencia_id'])->update([
                    'estado' => $estudianteData['estado'],
                    'comentario' => $estudianteData['comentario'],
                ]);
                $registrosActualizados++;
            } else {
                // Crear nuevo registro
                Asistencia::create([
                    'estudiante_id' => $estudianteData['id'],
                    'matricula_id' => $this->matriculaId,
                    'fecha' => $this->fecha,
                    'estado' => $estudianteData['estado'],
                    'comentario' => $estudianteData['comentario'],
                ]);
                $registrosCreados++;
            }
        }

        Notification::make()
            ->title('Asistencias guardadas exitosamente')
            ->body("Se crearon {$registrosCreados} nuevos registros y se actualizaron {$registrosActualizados} registros existentes.")
            ->success()
            ->send();

        // Recargar estudiantes para actualizar el estado
        $this->cargarEstudiantes();
    }

    public function updatedData($value, $key): void
    {
        if ($key === 'matricula_id') {
            $this->matriculaId = $value;
            $this->cargarEstudiantes();
        } elseif ($key === 'fecha') {
            $this->fecha = $value;
            $this->cargarEstudiantes();
        }
    }
}
