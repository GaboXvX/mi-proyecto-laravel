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
        Schema::create('reparaciones_incidencias', function (Blueprint $table) {
            $table->bigIncrements('id_reparacion'); // ID autoincremental
            $table->unsignedBigInteger('id_usuario'); // Relación con el usuario
            $table->unsignedBigInteger('id_incidencia'); 
            $table->unsignedBigInteger('id_personal_reparacion'); // Relación con el personal de reparación
            $table->string('slug')->unique(); // Slug único para la reparación
            $table->text('descripcion'); // Descripción de la reparación
            $table->string('prueba_fotografica'); // Ruta de la prueba fotográfica
            $table->timestamps(); // Timestamps para created_at y updated_at

            // Clave foránea
            $table->foreign('id_incidencia')->references('id_incidencia')->on('incidencias')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
            $table->foreign('id_personal_reparacion')->references('id_personal_reparacion')->on('personal_reparaciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reparaciones_incidencias');
    }
};
