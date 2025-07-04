<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regla extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hora_entrada',
        'hora_tardanza',
        'comentarios',
    ];

    /**
     * Relación con el modelo Matricula.
     * Una regla tiene muchas matrículas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }
}
