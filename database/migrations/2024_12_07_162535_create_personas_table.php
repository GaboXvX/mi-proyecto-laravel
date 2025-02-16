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
        Schema::create('personas', function (Blueprint $table) {
            $table->bigIncrements('id_persona');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_direccion');
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->bigInteger('cedula')->unique();
            $table->string('correo', 320)->unique();
            $table->bigInteger('telefono');
            $table->boolean('es_lider');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
