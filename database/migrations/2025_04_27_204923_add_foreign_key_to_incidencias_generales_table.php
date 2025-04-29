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
        Schema::table('incidencias_generales', function (Blueprint $table) {
            $table->foreign('id_direccion')->references('id_direccion')->on('direcciones')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
            $table->foreign('id_institucion')->references('id_institucion')->on('instituciones')->onDelete('cascade');
            $table->foreign('id_institucion_estacion')->references('id_institucion_estacion')->on('instituciones_estaciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias_generales', function (Blueprint $table) {
            //
        });
    }
};
