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
        Schema::table('model_has_roles', function (Blueprint $table) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                // Aseguramos que haya un campo user_id para la relación con la tabla users
                $table->unsignedBigInteger('user_id')->nullable()->after('model_type');

                // Definir la clave foránea con la tabla users
                $table->foreign('user_id')
                    ->references('id')->on('users') // Relaciona con el campo id de la tabla users
                    ->onDelete('cascade'); // Elimina los registros en model_has_roles si el usuario es eliminado
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            // Eliminar la relación de clave foránea en model_has_roles
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        });
    }
};
