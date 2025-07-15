<?php

namespace Database\Seeders;

use App\Models\Camara;
use App\Models\Matricula;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CamaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener las matrículas existentes
        $matriculas = Matricula::all();

        if ($matriculas->count() > 0) {
            // Crear algunas cámaras de ejemplo
            $camarasData = [
                [
                    'url_stream' => 'http://192.168.1.100:8080/stream',
                    'matricula_id' => $matriculas->first()->id,
                    'activo' => true,
                ],
                [
                    'url_stream' => 'http://192.168.1.101:8080/stream',
                    'matricula_id' => $matriculas->first()->id,
                    'activo' => false,
                ],
            ];

            // Si hay más de una matrícula, crear cámaras para la segunda también
            if ($matriculas->count() > 1) {
                $camarasData[] = [
                    'url_stream' => 'http://192.168.1.102:8080/stream',
                    'matricula_id' => $matriculas->skip(1)->first()->id,
                    'activo' => true,
                ];
            }

            foreach ($camarasData as $camaraData) {
                Camara::create($camaraData);
            }

            $this->command->info('Se han creado ' . count($camarasData) . ' cámaras de ejemplo.');
        } else {
            $this->command->warn('No se encontraron matrículas. Crea algunas matrículas primero.');
        }
    }
}
