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
        Schema::create('domicilios', function (Blueprint $table) {
            $table->bigIncrements('id_domicilio'); // Crea una columna 'id' como clave primaria
            $table->string('calle'); // Columna para la calle
            $table->string('manzana'); // Columna para la manzana
            $table->string('numero_de_casa'); // Columna para el número de casa
            $table->timestamps(); // Crea las columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domicilios');
    }
};
