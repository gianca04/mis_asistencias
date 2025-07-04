<?php

use App\Http\Controllers\Api\BiometricoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AsistenciaController;
use Illuminate\Support\Facades\Http;

// BIOMETRICOS

Route::post('/biometricos/registrar', [BiometricoController::class, 'registrarRostro']);

Route::get('/biometricos/matricula/{matricula_id}', [BiometricoController::class, 'obtenerRostrosPorMatricula']);

// ASISTENCIAS
Route::post('/asistencias/registro-masivo', [AsistenciaController::class, 'registrarDesdeReconocimiento']);

Route::get('/verificar-microservicio', function () {
    try {
        $response = Http::timeout(3)->get(env('FACE_SERVICE_URL') . '/status');

        if ($response->ok()) {
            return response()->json([
                'status' => 'ok',
                'microservicio' => $response->json()
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Microservicio no respondió correctamente'
            ], 500);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'No se pudo conectar con el microservicio',
            'error' => $e->getMessage()
        ], 500);
    }
});
