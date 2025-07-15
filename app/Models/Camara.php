<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Camara extends Model
{
    protected $fillable = [
        'url_stream',
        'matricula_id',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Relación con el modelo Matricula
     */
    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }
}
