<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('config_reglas_categorias', function (Blueprint $table) {
            $table->id('id_config');
            
            // Clave foránea a categorias_personas
            $table->unsignedBigInteger('id_categoria_persona');
            $table->foreign('id_categoria_persona')
                  ->references('id_categoria_persona')
                  ->on('categorias_personas')
                  ->onDelete('cascade');
            
            // Campos de configuración
            $table->boolean('requiere_comunidad')->default(false);
            $table->boolean('unico_en_comunidad')->default(false);
            $table->boolean('unico_en_sistema')->default(false);
            $table->string('mensaje_error')->nullable();
            
            $table->timestamps();
            
            // Asegurar que cada categoría tenga solo una configuración
            $table->unique('id_categoria_persona');
        });
    }

    public function down()
    {
        Schema::dropIfExists('config_reglas_categorias');
    }
};