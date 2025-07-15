<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camara;
use Illuminate\Http\JsonResponse;

class CamaraController extends Controller
{
    /**
     * Obtener todas las cámaras activas
     */
    public function camarasActivas(): JsonResponse
    {
        $camaras = Camara::with(['matricula.grado', 'matricula.seccion', 'matricula.regla'])
            ->where('activo', true)
            ->get()
            ->map(function ($camara) {
                return [
                    'id' => $camara->id,
                    'url_stream' => $camara->url_stream,
                    'matricula_id' => $camara->matricula_id,
                    'matricula' => [
                        'id' => $camara->matricula->id,
                        'codigo_matricula' => $camara->matricula->codigo_matricula,
                        'anio_escolar' => $camara->matricula->anio_escolar,
                        'grado' => $camara->matricula->grado?->nombre,
                        'seccion' => $camara->matricula->seccion?->nombre,
                        'regla' => $camara->matricula->regla ? [
                            'id' => $camara->matricula->regla->id,
                            'name' => $camara->matricula->regla->name,
                            'hora_entrada' => $camara->matricula->regla->hora_entrada,
                            'hora_tardanza' => $camara->matricula->regla->hora_tardanza,
                            'comentarios' => $camara->matricula->regla->comentarios,
                        ] : null,
                    ],
                    'activo' => $camara->activo,
                    'created_at' => $camara->created_at,
                    'updated_at' => $camara->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $camaras,
            'total' => $camaras->count(),
        ]);
    }
}
