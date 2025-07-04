<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'estudiantes';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'telefono',
        'direccion',
        'codigo_estudiante',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'dni' => 'string',
        'telefono' => 'string',
        'direccion' => 'string',
        'codigo_estudiante' => 'string',
    ];

    /**
     * Relación con el modelo Grado.
     * Un estudiante pertenece a un grado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    /**
     * Relación con el modelo Seccion.
     * Un estudiante pertenece a una sección.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    // App\Models\Estudiante.php

    public function matriculas()
    {
        return $this->belongsToMany(Matricula::class, 'estudiante_matricula', 'estudiante_id', 'matricula_id');
    }
    // En el modelo Estudiante
    public function biometrico()
    {
        return $this->hasOne(Biometrico::class);
    }


    /**
     * Get the full name of the student.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return ucfirst($this->nombre) . ' ' . ucfirst($this->apellido);
    }
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
