<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes');  // Relación con la tabla 'estudiantes'
            $table->foreignId('matricula_id')->constrained('matriculas');  // Relación con la tabla 'matriculas'
            $table->date('fecha');  // Fecha de la asistencia
            $table->enum('estado', ['tardanza', 'falta', 'justificado']);  // Estado de la asistencia (tardanza, falta, justificado)
            $table->text('comentario')->nullable();  // Comentario adicional, puede ser nulo
            $table->timestamps();  // Timestamps para mantener la fecha de creación y actualización
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
