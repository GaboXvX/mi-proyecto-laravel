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
            $table->string('nombre');
            $table->string('apellido');
            $table->biginteger('cedula');
            $table->char('genero');
            $table->string('telefono');
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
