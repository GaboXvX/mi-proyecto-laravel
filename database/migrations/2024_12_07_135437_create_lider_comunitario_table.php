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
<<<<<<< HEAD
=======
            $table->unsignedBigInteger('id_direccion');
>>>>>>> 6274081162731933fa5a1f461cf7cde9adc29d56
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_comunidad');
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->bigInteger('cedula')->unique();
            $table->bigInteger('telefono');
            $table->string('correo','320')->unique();
<<<<<<< HEAD
            $table->string('calle');
            $table->string('manzana');
            $table->string('numero_de_casa');
=======
>>>>>>> 6274081162731933fa5a1f461cf7cde9adc29d56
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
