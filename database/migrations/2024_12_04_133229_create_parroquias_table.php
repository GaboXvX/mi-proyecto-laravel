<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_parroquias_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParroquiasTable extends Migration
{
    public function up()
    {
        Schema::create('parroquias', function (Blueprint $table) {
            $table->bigIncrements('id_parroquia');  // Se crea la columna id como clave primaria
            $table->string('nombre');  // Nombre de la parroquia
            $table->timestamps();  // Campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('parroquias');
    }
}
