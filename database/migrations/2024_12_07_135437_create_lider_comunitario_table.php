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
        Schema::create('lider_comunitario', function (Blueprint $table) {
            $table->bigIncrements('id_lider');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_comunidad');
            $table->unsignedBigInteger('id_direccion');
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->bigInteger('cedula')->unique();
            $table->bigInteger('telefono');
            $table->string('correo','320')->unique();
            $table->timestamps();
            $table->foreign('id_comunidad')
                  ->references('id_comunidad')
                  ->on('comunidades')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lider_comunitario');
    }
};
