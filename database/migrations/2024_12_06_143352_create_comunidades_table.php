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
            $table->unsignedBigInteger('id_sector');  // Clave foránea para sectores
            $table->foreign('id_sector')
            ->references('id_sector')
            ->on('sectores')
            ->onDelete('cascade');
                 
            $table->timestamps();  
        });
    }

    public function down()
    {
        Schema::dropIfExists('comunidades');
    }
}

