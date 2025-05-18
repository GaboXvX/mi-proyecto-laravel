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
        Schema::create('direcciones_incidencias', function (Blueprint $table) {
            $table->bigIncrements('id_direccion_incidencia')->unsigned(); // Clave primaria
            $table->unsignedBigInteger('id_estado');
            $table->unsignedBigInteger('id_municipio');
            $table->bigInteger('id_parroquia')->unsigned();
            $table->bigInteger('id_urbanizacion')->unsigned();
            $table->bigInteger('id_sector')->unsigned();
            $table->bigInteger('id_comunidad')->unsigned();
            $table->string('calle');
            $table->string('punto_de_referencia');
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

