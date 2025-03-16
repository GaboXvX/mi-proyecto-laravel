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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->bigIncrements('id_direccion')->unsigned(); // Clave primaria
            $table->unsignedBigInteger('id_persona');
            $table->unsignedBigInteger('id_estado');
            $table->unsignedBigInteger('id_municipio');
            $table->bigInteger('id_parroquia')->unsigned()->nullable();
            $table->bigInteger('id_urbanizacion')->unsigned()->nullable();
            $table->bigInteger('id_sector')->unsigned()->nullable();
            $table->bigInteger('id_comunidad')->unsigned()->nullable();
            $table->string('calle');
            $table->string('manzana');
            $table->string('bloque')->nullable();
            $table->integer('numero_de_vivienda');
            $table->boolean('es_principal');
            $table->timestamps();
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};

