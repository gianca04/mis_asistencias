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
        Schema::table('biometricos', function (Blueprint $table) {
            // Cambiar tipo de columna 'modelo_facial' de string a json
            $table->json('modelo_facial')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biometricos', function (Blueprint $table) {
            // Revertir de json a string si es necesario
            $table->string('modelo_facial')->nullable()->change();
        });
    }
};
