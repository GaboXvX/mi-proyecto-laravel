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
            $table->unsignedBigInteger('id_rol');
            $table->unsignedBigInteger('id_pregunta');
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->biginteger('cedula');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('nombre_usuario')->unique();
            $table->string('password');
            $table->string('estado');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('id_pregunta')->references('id_pregunta')  ->on('preguntas_de_seguridad')  ->onDelete('cascade');
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
