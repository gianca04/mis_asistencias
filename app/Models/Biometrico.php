<?php

namespace App\Models;

use App\Http\Controllers\Api\BiometricoController;
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

        // Cuando un nuevo biométrico es creado, registrar rostro automáticamente
        static::created(function ($biometrico) {
            // Instanciamos el controlador
            $biometricoController = new BiometricoController();

            // Ruta correcta de la imagen
            $fotoFrontalPath = storage_path('app/public/' . $biometrico->foto_frontal);

            // Verificamos que la imagen exista
            if (!file_exists($fotoFrontalPath)) {
                Log::error('La imagen del rostro no existe en la ruta: ' . $fotoFrontalPath);
                return;
            }

            // Simulamos la petición para registrar el rostro
            $request = request()->merge([
                'estudiante_id' => $biometrico->estudiante_id,
                'foto_frontal' => $fotoFrontalPath // Ruta completa de la imagen almacenada
            ]);

            // Llamamos al método registrarRostro
            try {
                // Usamos el controlador para registrar el rostro
                $biometricoController->registrarRostro($request);
            } catch (\Exception $e) {
                Log::error('Error al registrar el rostro automáticamente: ' . $e->getMessage());
            }
        });
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
