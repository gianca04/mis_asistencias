<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Estudiante;
use App\Models\Matricula;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;  // Importar el logger

class AsistenciaController extends Controller
{
    public function registrarDesdeReconocimiento(Request $request)
    {
        // Validación de entrada
        $request->validate([
            'matricula_id' => 'required|exists:matriculas,id',
            'rostros_detectados' => 'required|array|min:1',
            'rostros_detectados.*.id' => 'required|string|starts_with:alumno_',
            'captura' => 'nullable|date',
        ]);

        // Registro de fecha y matrícula
        $fecha = $request->input('captura') ?? Carbon::now()->toDateTimeString();
        $matriculaId = $request->input('matricula_id');

        // Log de fecha y matrícula
        Log::info('Fecha de captura: ' . $fecha);
        Log::info('Matrícula ID: ' . $matriculaId);

        $asistencias = [];

        // Iterar sobre los rostros detectados
        foreach ($request->rostros_detectados as $rostro) {
            // Extraer ID del estudiante
            $estudianteId = intval(str_replace('alumno_', '', $rostro['id']));

            // Log para cada rostro detectado
            Log::info('Procesando rostro con ID: ' . $estudianteId);

            // Verificar si ya se registró asistencia para el estudiante en ese día
            $yaRegistrado = Asistencia::where('estudiante_id', $estudianteId)
                ->whereDate('fecha', Carbon::parse($fecha)->toDateString())
                ->where('matricula_id', $matriculaId)
                ->exists();

            // Log si ya está registrado
            if ($yaRegistrado) {
                Log::info('Ya existe asistencia para el estudiante ' . $estudianteId . ' en la fecha ' . $fecha);
            } else {
                // Crear nueva asistencia
                $asistencias[] = Asistencia::create([
                    'estudiante_id' => $estudianteId,
                    'matricula_id' => $matriculaId,
                    'fecha' => $fecha,
                    'estado' => 'presente',
                    'comentario' => 'Asistencia automática por reconocimiento facial',
                ]);
                Log::info('Asistencia registrada para el estudiante ' . $estudianteId);
            }
        }

        // Responder con los detalles del registro
        Log::info('Asistencias registradas: ' . count($asistencias));
        return response()->json([
            'message' => 'Asistencias registradas',
            'registradas' => count($asistencias),
            'fecha' => $fecha,
        ]);
    }
}
