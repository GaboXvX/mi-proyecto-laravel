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
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('nacionalidad');
            $table->bigInteger('cedula')->unique();
            $table->char('genero');
            $table->string('correo', 320)->unique();
            $table->bigInteger('telefono');
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
