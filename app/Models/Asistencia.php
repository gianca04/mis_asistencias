<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'estudiante_id',
        'matricula_id',
        'fecha',
        'estado',
        'comentario',
    ];

    /**
     * Relación con el modelo Estudiante.
     * Una asistencia pertenece a un estudiante.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    /**
     * Relación con el modelo Matricula.
     * Una asistencia pertenece a una matrícula.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    /**
     * Obtener el estado de la asistencia con formato específico.
     *
     * @return string
     */
    public function getEstadoFormattedAttribute()
    {
        return ucfirst($this->estado);
    }
}
