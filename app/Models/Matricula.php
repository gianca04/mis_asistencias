<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'matriculas';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'regla_id',
        'codigo_matricula',
        'grado_id',
        'seccion_id',
        'user_id',
        'anio_escolar',
        'matricula_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'grado_id' => 'integer',
        'seccion_id' => 'integer',
        'user_id' => 'integer',
        'anio_escolar' => 'string',
        'matricula_id' => 'integer',

    ];

    /**
     * Relación con el modelo Grado.
     * Una matrícula pertenece a un grado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    /**
     * Relación con el modelo Seccion.
     * Una matrícula pertenece a una sección.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    /**
     * Relación con el modelo User.
     * Una matrícula está asociada a un profesor (user).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profesor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // En el modelo Matricula
    public function regla()
    {
        return $this->belongsTo(Regla::class);
    }

    /**
     * Obtener el código de matrícula con formato específico.
     *
     * @return string
     */
    public function getFormattedCodigoMatriculaAttribute()
    {
        return strtoupper($this->codigo_matricula);
    }
    // App\Models\Matricula.php


    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_matricula', 'matricula_id', 'estudiante_id');
    }
}
