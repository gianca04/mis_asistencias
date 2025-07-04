<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Biometrico extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'biometricos';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'estudiante_id',
        'foto_perfil',
        'foto_frontal',
        'modelo_facial',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'estudiante_id' => 'integer',
        'foto_perfil' => 'string',
        'foto_frontal' => 'string',
        'modelo_facial' => 'array',
    ];

    /**
     * Relación con el modelo Estudiante.
     * Un biometrico pertenece a un estudiante.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }


    protected static function boot()
    {
        parent::boot();

        // Cuando un nuevo biométrico es creado, procesar rostro automáticamente
        static::created(function ($biometrico) {
            // Solo procesar si tiene foto_frontal
            if ($biometrico->foto_frontal) {
                try {
                    $biometrico->procesarModeloFacial();
                } catch (\Exception $e) {
                    Log::error('Error al procesar modelo facial en creación: ' . $e->getMessage());
                }
            }
        });

        // Cuando un biométrico es actualizado, verificar si cambió la foto_frontal
        static::updated(function ($biometrico) {
            // Verificar si la foto_frontal fue modificada
            if ($biometrico->wasChanged('foto_frontal') && $biometrico->foto_frontal) {
                try {
                    $biometrico->procesarModeloFacial();
                    Log::info("Modelo facial actualizado para biométrico ID: {$biometrico->id}");
                } catch (\Exception $e) {
                    Log::error('Error al procesar modelo facial en actualización: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Procesa el modelo facial para este biométrico
     */
    public function procesarModeloFacial()
    {
        // Construir la ruta completa de la imagen
        $fotoFrontalPath = storage_path('app/public/' . $this->foto_frontal);

        // Verificar que la imagen exista
        if (!file_exists($fotoFrontalPath)) {
            Log::error('La imagen del rostro no existe en la ruta: ' . $fotoFrontalPath);
            throw new \Exception('Imagen no encontrada: ' . $fotoFrontalPath);
        }

        // Llamar al microservicio Flask para obtener el encoding
        $response = \Illuminate\Support\Facades\Http::attach(
            'file',
            file_get_contents($fotoFrontalPath),
            basename($fotoFrontalPath)
        )->timeout(10)->post(env('FACE_SERVICE_URL') . '/encoding');

        if (!$response->ok()) {
            Log::error('Error al comunicarse con el microservicio Flask.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Error al comunicarse con el microservicio Flask');
        }

        $encoding = $response->json('encoding');

        if (!$encoding) {
            Log::warning("Encoding no retornado desde Flask", ['response' => $response->json()]);
            throw new \Exception('No se obtuvo encoding válido del microservicio');
        }

        // Actualizar el modelo facial
        $this->update(['modelo_facial' => $encoding]);

        Log::info("Modelo facial procesado exitosamente para biométrico ID: {$this->id}");

        return $encoding;
    }




    /**
     * Obtener la URL de la foto frontal.
     *
     * @return string
     */
    public function getFotoFrontalUrlAttribute()
    {
        return $this->foto_frontal ? asset('storage/' . $this->foto_frontal) : null;
    }
}
