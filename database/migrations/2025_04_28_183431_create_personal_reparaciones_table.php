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
        Schema::create('personal_reparaciones', function (Blueprint $table) {
            $table->bigIncrements('id_personal_reparacion');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_institucion');
            $table->unsignedBigInteger('id_institucion_estacion');
            $table->string('slug')->unique();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('nacionalidad');
            $table->string('cedula', 20)->unique();
            $table->string('telefono', 20);
            $table->timestamps();
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
        Schema::dropIfExists('personal_reparaciones');
    }
};
