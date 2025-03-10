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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->unsignedBigInteger('id_estado_usuario')->nullable();
            $table->unsignedBigInteger('id_rol');
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->biginteger('cedula');
            $table->string('apellido');
            $table->char('genero');
            $table->date('fecha_nacimiento');
            $table->double('altura');
            $table->string('email')->unique();
            $table->string('nombre_usuario')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
