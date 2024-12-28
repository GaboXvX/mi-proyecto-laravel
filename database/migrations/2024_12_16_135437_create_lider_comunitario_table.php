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
            $table->unsignedBigInteger('id_direccion');
            $table->unsignedBigInteger('id_domicilio');
            $table->unsignedBigInteger('id_usuario');
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->integer('cedula')->unique();
            $table->bigInteger('telefono');
            $table->string('correo','320')->unique();
            $table->timestamps();
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
