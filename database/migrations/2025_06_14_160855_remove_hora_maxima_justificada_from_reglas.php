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
        // Eliminar la columna hora_maxima_justificada
        Schema::table('reglas', function (Blueprint $table) {
            $table->dropColumn('hora_maxima_justificada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Volver a agregar la columna hora_maxima_justificada en caso de reversión
        Schema::table('reglas', function (Blueprint $table) {
            $table->time('hora_maxima_justificada')->nullable();  // Re-crea la columna con las mismas características
        });
    }
};
