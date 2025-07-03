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
        Schema::create('biometricos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiante_id')->unique();
            $table->foreign('estudiante_id')->references('id')->on('estudiantes')->onDelete('cascade');

            $table->string('foto_perfil')->nullable();
            $table->string('foto_frontal')->nullable();

            $table->string('modelo_facial')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometricos');
    }
};
