<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Tabla relacionada con el modelo
    protected $table = 'roles';

    // Atributos que se pueden asignar masivamente
    protected $fillable = [
        'name',
        'guard_name',
    ];

    // Relación con los usuarios
    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id');
    }
}
