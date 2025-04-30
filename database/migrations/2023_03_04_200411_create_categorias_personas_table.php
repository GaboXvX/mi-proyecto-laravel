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
        Schema::create('categorias_personas', function (Blueprint $table) {
            $table->bigIncrements('id_categoria_persona');
            $table->string('nombre_categoria');
            $table->string('slug')->unique(); // Para búsquedas amigables
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_personas');
    }
};
