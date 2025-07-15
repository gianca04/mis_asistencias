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

    /**
     * Obtener una representación legible de la matrícula
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        $grado = $this->grado?->nombre ?? 'Grado ' . $this->grado_id;
        $seccion = $this->seccion?->nombre ?? 'Sección ' . $this->seccion_id;
        $anio = $this->anio_escolar;

        return "{$grado} - {$seccion} ({$anio})";
    }
    // App\Models\Matricula.php


    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_matricula', 'matricula_id', 'estudiante_id');
    }

    /**
     * Relación con el modelo Asistencia.
     * Una matrícula tiene muchas asistencias.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Relación con el modelo Camara.
     * Una matrícula puede tener muchas cámaras.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function camaras()
    {
        return $this->hasMany(Camara::class);
    }

    // En el modelo Matricula.php
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($matricula) {
            if ($matricula->grado_id && $matricula->seccion_id && $matricula->anio_escolar) {
                $matricula->codigo_matricula = $matricula->generateCodigoMatricula();
            } else {
                throw new \Exception('Faltan datos para generar el código de matrícula.');
            }
        });

        static::updating(function ($matricula) {
            if ($matricula->grado_id && $matricula->seccion_id && $matricula->anio_escolar) {
                $matricula->codigo_matricula = $matricula->generateCodigoMatricula();
            } else {
                throw new \Exception('Faltan datos para generar el código de matrícula.');
            }
        });
    }

    public function generateCodigoMatricula()
    {
        $grado = $this->grado_id;
        $seccion = $this->seccion_id ? Seccion::find($this->seccion_id)?->nombre : '';
        $anioEscolar = $this->anio_escolar ?? now()->year;

        if (!$grado || !$seccion || !$anioEscolar) {
            throw new \Exception('Faltan datos para generar el código de matrícula.');
        }

        return $anioEscolar . strtoupper($grado) . strtoupper($seccion);
    }
}
