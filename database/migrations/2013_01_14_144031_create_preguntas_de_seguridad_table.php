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
        Schema::create('preguntas_de_seguridad', function (Blueprint $table) {
            $table->bigIncrements('id_pregunta');
           $table->string('primera_mascota');
           $table->string('ciudad_de_nacimiento');
           $table->string('nombre_de_mejor_amigo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preguntas_de_seguridad');
    }
};
