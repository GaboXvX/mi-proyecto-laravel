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
        Schema::create('empleados_autorizados', function (Blueprint $table) {
            $table->bigIncrements('id_empleado_autorizado');
            $table->unsignedBigInteger('id_cargo');
            $table->unsignedBigInteger('id_departamento');
            $table->string('nombre');
            $table->string('apellido');
            $table->integer('cedula');
            $table->string('genero');
            $table->string('telefono');
            $table->string('correo')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados_autorizados');
    }
};
