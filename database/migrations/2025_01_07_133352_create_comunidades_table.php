<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_comunidades_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComunidadesTable extends Migration
{
    public function up()
    {
        Schema::create('comunidades', function (Blueprint $table) {
            $table->bigIncrements('id_comunidad');  // Clave primaria para la comunidad
            $table->string('nombre');  // Nombre de la comunidad
            $table->foreignId('id_sector')  // Clave foránea para sectores
                  ->constrained('sectores')  // Relación con la tabla sectores
                  ->onDelete('cascade');  // Borrado en cascada
                  $table->unsignedBigInteger('id_lider');
                  $table->foreign('id_lider')
                        ->references('id_lider')
                        ->on('lider_comunitario')
                        ->onDelete('cascade');
            $table->timestamps();  // Campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('comunidades');
    }
}

