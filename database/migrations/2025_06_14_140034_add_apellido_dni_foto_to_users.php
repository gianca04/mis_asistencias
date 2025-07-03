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
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido')->nullable()->after('name'); // Campo apellido
            $table->string('dni')->nullable()->unique()->after('apellido'); // Campo dni, único
            $table->string('foto')->nullable()->after('dni'); // Campo foto, puede almacenar la ruta de la imagen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar los campos si se hace rollback
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('apellido');
            $table->dropColumn('dni');
            $table->dropColumn('foto');
        });
    }
};
