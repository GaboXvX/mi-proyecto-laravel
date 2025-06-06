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
            $table->bigIncrements('id_domicilio')->unsigned(); // Clave primaria
            $table->unsignedBigInteger('id_persona') ;
            $table->unsignedBigInteger('id_estado');
            $table->unsignedBigInteger('id_municipio');
            $table->bigInteger('id_parroquia')->unsigned();
            $table->bigInteger('id_urbanizacion')->unsigned();
            $table->bigInteger('id_sector')->unsigned();
            $table->bigInteger('id_comunidad')->unsigned();
            $table->string('calle');
            $table->string('manzana')->nullable();
            $table->string('bloque')->nullable();
            $table->string('numero_de_vivienda');
            $table->boolean('es_principal');
            $table->timestamps();
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
