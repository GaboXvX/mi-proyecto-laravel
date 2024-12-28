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
            $table->unsignedBigInteger('id_persona')->nullable();
            $table->unsignedBigInteger('id_lider')->nullable();
           $table->string('slug')->unique();
            $table->string('tipo_incidencia');
            $table->text('descripcion');
            $table->integer('nivel_prioridad');
            $table->string('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
