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
        Schema::table('reglas', function (Blueprint $table) {
            // Eliminar la columna 'matricula_id'
            $table->dropForeign(['matricula_id']);
            $table->dropColumn('matricula_id');

            // Agregar la nueva columna 'name'
            $table->string('name')->nullable()->comment('Nombre de la regla');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reglas', function (Blueprint $table) {
            // Volver a agregar la columna 'matricula_id'
            $table->foreignId('matricula_id')->constrained('matriculas');

            // Eliminar la columna 'name'
            $table->dropColumn('name');
        });
    }
};
