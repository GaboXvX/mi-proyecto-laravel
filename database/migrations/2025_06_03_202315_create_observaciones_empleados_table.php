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
      Schema::create('observaciones_empleados', function (Blueprint $table) {
    $table->bigIncrements('id_observacion_empleado');
    $table->unsignedBigInteger('id_empleado_autorizado');
    $table->text('observacion');
    $table->string('tipo', 20); // 'retiro' o 'incorporacion'
    $table->timestamps();
    
    $table->foreign('id_empleado_autorizado')
          ->references('id_empleado_autorizado')
          ->on('empleados_autorizados')
          ->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observaciones_empleados');
    }
};
