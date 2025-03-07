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
        Schema::create('peticiones', function (Blueprint $table) {
            $table->bigIncrements('id_peticion');
            $table->unsignedBigInteger('id_rol');
            $table->string('slug');
            $table->string('estado_peticion');
            $table->string('nombre');
            $table->biginteger('cedula');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('nombre_usuario')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peticiones');
    }
};
