<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckFaceServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar la conectividad con el servicio de reconocimiento facial';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $serviceUrl = config('services.face_service.url');

        $this->info("Verificando conectividad con el servicio de reconocimiento facial...");
        $this->info("URL del servicio: {$serviceUrl}");

        try {
            $response = Http::timeout(10)->get($serviceUrl . '/health');

            if ($response->successful()) {
                $this->info("✅ Servicio de reconocimiento facial está disponible");
                $this->info("Código de respuesta: " . $response->status());

                if ($response->json()) {
                    $this->info("Datos de respuesta:");
                    $this->table(
                        ['Campo', 'Valor'],
                        collect($response->json())->map(function ($value, $key) {
                            return [$key, is_array($value) ? json_encode($value) : $value];
                        })->toArray()
                    );
                }
            } else {
                $this->error("❌ El servicio respondió con código: " . $response->status());
            }

        } catch (\Exception $e) {
            $this->error("❌ No se pudo conectar al servicio de reconocimiento facial");
            $this->error("Error: " . $e->getMessage());
            $this->warn("Asegúrate de que el servicio esté ejecutándose en: {$serviceUrl}");
        }

        return 0;
    }
}
