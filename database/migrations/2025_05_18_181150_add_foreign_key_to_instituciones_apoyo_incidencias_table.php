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
        Schema::table('instituciones_apoyo_incidencias', function (Blueprint $table) {
            // Agregar la clave foránea para id_institucion
            $table->foreign('id_institucion')
                ->references('id_institucion')
                ->on('instituciones')
                ->onDelete('cascade'); // Eliminar registros relacionados si se elimina la institución

            // Agregar la clave foránea para id_incidenia
            $table->foreign('id_incidencia')
                ->references('id_incidencia')
                ->on('incidencias')
                ->onDelete('cascade'); // Eliminar registros relacionados si se elimina la incidencia
        // Agregar la clave foránea para id_nivel_incidencia
            $table->foreign('id_institucion_estacion')
                ->references('id_institucion_estacion')
                ->on('instituciones_estaciones')
                ->onDelete('cascade'); // Eliminar registros relacionados si se elimina la estación de la institución
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instituciones_apoyo_incidencias', function (Blueprint $table) {
            //
        });
    }
};
