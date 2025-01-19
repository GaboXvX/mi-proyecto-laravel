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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->bigIncrements('id_movimiento');
            $table->unsignedBigInteger('id_incidencia')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_persona')->nullable();
            $table->unsignedBigInteger('id_lider')->nullable();
            $table->text('valor_anterior');
            $table->text('valor_nuevo')->nullable();
            $table->string('accion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
