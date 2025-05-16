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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->bigIncrements('id_incidencia');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_persona')->nullable();
            $table->unsignedBigInteger('id_direccion_incidencia');
            $table->unsignedBigInteger('id_categoria_exclusiva')->nullable();
            $table->unsignedBigInteger('id_institucion');
            $table->unsignedBigInteger('id_institucion_estacion')->nullable();
            $table->unsignedBigInteger('id_nivel_incidencia')->nullable();
            $table->unsignedBigInteger('id_estado_incidencia')->nullable();
            $table->unsignedBigInteger('id_tipo_incidencia');
            $table->string('slug')->unique();
            $table->string('cod_incidencia')->unique();
            $table->text('descripcion');
            $table->dateTime('fecha_vencimiento');
            $table->string('ultimo_recordatorio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias_personas');
    }
};
