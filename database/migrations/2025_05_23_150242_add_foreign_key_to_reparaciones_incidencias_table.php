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
        Schema::table('reparaciones_incidencias', function (Blueprint $table) {
            // Añadir la clave foránea a la tabla reparaciones_incidencias
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
        Schema::table('reparaciones_incidencias', function (Blueprint $table) {
            //
        });
    }
};
