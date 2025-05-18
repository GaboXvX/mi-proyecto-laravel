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
        Schema::table('incidencias', function (Blueprint $table) {
            $table->foreign('id_persona')->references('id_persona')->on('personas')->onDelete('cascade');
            $table->foreign('id_direccion_incidencia')->references('id_direccion_incidencia')->on('direcciones_incidencias')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
            $table->foreign('id_institucion')->references('id_institucion')->on('instituciones')->onDelete('cascade');
            $table->foreign('id_institucion_estacion')->references('id_institucion_estacion')->on('instituciones_estaciones')->onDelete('cascade');
            $table->foreign('id_estado_incidencia')->references('id_estado_incidencia')->on('estados_incidencias')->onDelete('cascade');
            $table->foreign('id_nivel_incidencia')->references('id_nivel_incidencia')->on('niveles_incidencias')->onDelete('cascade');
            $table->foreign('id_tipo_incidencia')->references('id_tipo_incidencia')->on('tipos_incidencias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias_personas', function (Blueprint $table) {
            $table->dropColumn('id_persona');
        });
    }
};
