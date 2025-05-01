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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->bigIncrements('id_movimiento');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_usuario_afectado')->nullable();
            $table->foreign('id_usuario_afectado')->references('id_usuario')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_persona')->nullable();
            $table->foreign('id_persona')->references('id_persona')->on('personas')->onDelete('cascade');
            $table->unsignedBigInteger('id_direccion')->nullable();
            $table->foreign('id_direccion')->references('id_direccion')->on('direcciones')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_incidencia')->nullable();
            $table->foreign('id_incidencia')->references('id_incidencia')->on('incidencias')->onDelete('cascade');
            $table->string('descripcion', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
