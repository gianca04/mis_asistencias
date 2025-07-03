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
        Schema::create('reglas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas');  // Relación con la matrícula (si es específico por matrícula)
            $table->time('hora_entrada');  // Hora de entrada (ejemplo: 08:00:00)
            $table->time('hora_tardanza');  // Hora de límite para considerarse tardanza (ejemplo: 08:15:00)
            $table->time('hora_maxima_justificada')->nullable();  // Hora máxima para justificar la ausencia (opcional)
            $table->text('comentarios')->nullable();  // Comentarios adicionales sobre las reglas de asistencia
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas');
    }
};
