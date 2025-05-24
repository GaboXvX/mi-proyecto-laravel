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
               // Relación con la prueba fotográfica
            $table->string('slug')->unique(); // Slug único para la reparación
            $table->text('descripcion',350); // Descripción de la reparación
            $table->timestamps(); // Timestamps para created_at y updated_at

            // Clave foránea
           
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
