<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Biometrico;
use App\Models\Matricula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BiometricoController extends Controller
{
    public function obtenerRostrosPorMatricula($matricula_id)
    {
        $matricula = Matricula::with('estudiantes.biometrico')->find($matricula_id);

        if (!$matricula) {
            return response()->json([
                'error' => 'Matrícula no encontrada.'
            ], Response::HTTP_NOT_FOUND);
        }

        $rostros = [];

        foreach ($matricula->estudiantes as $estudiante) {
            $biometrico = $estudiante->biometrico;

            if ($biometrico && $biometrico->modelo_facial) {
                $rostros[] = [
                    'id' => 'alumno_' . $estudiante->id,
                    'encoding' => $biometrico->modelo_facial,
                ];
            }
        }

        return response()->json([
            'matricula_id' => $matricula_id,
            'total' => count($rostros),
            'rostros' => $rostros,
        ]);
    }

    public function registrarRostro(Request $request)
    {
        $validated = $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'foto_frontal' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $foto = $request->file('foto_frontal');
            $rutaImagen = $foto->store('rostros', 'public');

            Log::info("Imagen almacenada: $rutaImagen");

            $response = Http::attach(
                'file',
                file_get_contents($foto),
                $foto->getClientOriginalName()
            )->timeout(10)->post(env('FACE_SERVICE_URL') . '/encoding');

            if (!$response->ok()) {
                Log::error('Error al comunicarse con el microservicio Flask.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'error' => 'No se pudo procesar el rostro. Verifica el microservicio.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $encoding = $response->json('encoding');

            if (!$encoding) {
                Log::warning("Encoding no retornado desde Flask", ['response' => $response->json()]);
                return response()->json(['error' => 'No se obtuvo encoding válido.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $biometrico = Biometrico::updateOrCreate(
                ['estudiante_id' => $validated['estudiante_id']],
                [
                    'foto_frontal' => $rutaImagen,
                    'modelo_facial' => $encoding,
                ]
            );

            return response()->json([
                'message' => 'Rostro registrado correctamente.',
                'biometrico' => $biometrico,
            ]);
        } catch (\Exception $e) {
            Log::error('Excepción al registrar rostro', ['exception' => $e]);
            return response()->json([
                'error' => 'Ocurrió un error interno al registrar el rostro.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
