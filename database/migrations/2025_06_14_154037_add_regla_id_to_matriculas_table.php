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
        Schema::table('matriculas', function (Blueprint $table) {
            // Agregar la columna 'regla_id' como clave foránea
            $table->unsignedBigInteger('regla_id')->nullable()->after('user_id');  // Coloca esta columna después de 'user_id'
            $table->foreign('regla_id')->references('id')->on('reglas')->onDelete('set null');  // Relación con 'reglas'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matriculas', function (Blueprint $table) {
            //
                        // Eliminar la clave foránea y la columna 'regla_id'
            $table->dropForeign(['regla_id']);
            $table->dropColumn('regla_id');
        });
    }
};
