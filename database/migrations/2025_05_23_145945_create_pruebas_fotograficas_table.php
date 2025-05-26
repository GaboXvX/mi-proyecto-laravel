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
        Schema::create('pruebas_fotograficas', function (Blueprint $table) {
            $table->bigIncrements('id_prueba_fotografica'); // ID autoincremental
            $table->unsignedBigInteger('id_incidencia')->nullable();// Relación con la incidencia
            $table ->unsignedBigInteger('id_reparacion')->nullable(); // Relación con el usuario
            $table->string('observacion', 50); // Nombre de la prueba fotográfica
            $table->string('ruta')->nullable(); // Ruta de la foto (opcional)
            $table->string('etapa_foto'); // Descripción de la prueba fotográfica
            $table->timestamps(); // Timestamps para created_at y updated_at

            // Clave foránea
            $table->foreign('id_incidencia')->references('id_incidencia')->on('incidencias')->onDelete('cascade');
            $table->foreign('id_reparacion')->references('id_reparacion')->on('reparaciones_incidencias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pruebas_fotograficas');
    }
};
