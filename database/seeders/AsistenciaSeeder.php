<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asistencia;
use Carbon\Carbon;

class AsistenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estudiantes = range(36, 68); // IDs de estudiantes
        $matriculaId = 2; // ID de matrícula
        $estados = ['tardanza', 'falta', 'justificado', 'presente'];

        $fechas = [
            '2025-06-30', // Lunes
            '2025-07-01', // Martes
            '2025-07-02', // Miércoles
            '2025-07-03', // Jueves
            '2025-07-04', // Viernes
        ];

        foreach ($fechas as $fecha) {
            foreach ($estudiantes as $estudianteId) {
                Asistencia::create([
                    'estudiante_id' => $estudianteId,
                    'matricula_id' => $matriculaId,
                    'fecha' => $fecha,
                    'estado' => $estados[array_rand($estados)],
                    'comentario' => null,
                    'created_at' => Carbon::parse($fecha)->startOfDay(),
                    'updated_at' => Carbon::parse($fecha)->startOfDay(),
                ]);
            }
        }
    }
}
