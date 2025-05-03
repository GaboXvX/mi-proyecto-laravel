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
        Schema::create('instituciones_estaciones', function (Blueprint $table) {
            $table->bigIncrements('id_institucion_estacion');
            $table->unsignedBigInteger('id_institucion');
            $table->foreign('id_institucion')
                  ->references('id_institucion')
                  ->on('instituciones')
                  ->onDelete('cascade');
                  $table->unsignedBigInteger('id_estado');
            $table->foreign('id_estado')->references('id_estado')->on('estados')->onDelete('cascade');
                  $table->unsignedBigInteger('id_municipio');
            $table->foreign('id_municipio')
                  ->references('id_municipio')
                  ->on('municipios')
                  ->onDelete('cascade');
                  $table->string('nombre')->unique();
                  $table->string('codigo_estacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instituciones_estaciones');
    }
};
