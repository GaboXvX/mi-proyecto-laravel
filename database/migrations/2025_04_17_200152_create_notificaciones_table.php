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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->bigIncrements('id_notificacion');
            $table->unsignedBigInteger('id_usuario')->nullable(); // Quien genera la notificación
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
            $table->string('tipo_notificacion');
            $table->string('titulo');
            $table->string('mensaje',320);
            $table->boolean('mostrar_a_todos')->default(false);
            $table->unsignedBigInteger('id_incidencia')->nullable();
            $table->foreign('id_incidencia')->references('id_incidencia')->on('incidencias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
