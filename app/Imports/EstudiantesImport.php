<?php

namespace App\Imports;

use App\Models\Estudiante;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EstudiantesImport implements ToModel, WithHeadingRow, WithValidation
{
    public $importedStudents = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $estudiante = Estudiante::firstOrCreate(
            ['dni' => $row['dni']],
            [
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'telefono' => $row['telefono'] ?? null,
                'direccion' => $row['direccion'] ?? null,
                'codigo_estudiante' => $row['codigo_estudiante'],
            ]
        );
        
        $this->importedStudents[] = $estudiante;
        
        return $estudiante;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|size:8',
            'codigo_estudiante' => 'required|string|max:255',
            'telefono' => 'nullable|string|size:9',
            'direccion' => 'nullable|string|max:255',
        ];
    }
    
    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'dni.required' => 'El DNI es obligatorio',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos',
            'codigo_estudiante.required' => 'El código de estudiante es obligatorio',
            'telefono.size' => 'El teléfono debe tener exactamente 9 dígitos',
        ];
    }
}
