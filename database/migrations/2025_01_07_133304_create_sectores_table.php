<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_sectores_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectoresTable extends Migration
{
    public function up()
    {
        Schema::create('sectores', function (Blueprint $table) {
            $table->bigIncrements('id_sector');  // Se crea la columna id como clave primaria
            $table->string('nombre');  // Nombre del sector
            $table->foreignId('id_urbanizacion')  // Clave foránea con nombre explícito
                  ->constrained('urbanizaciones')  // Relacionado con la tabla urbanizaciones
                  ->onDelete('cascade');  // Borrado en cascada
            $table->timestamps();  // Campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('sectores');
    }
}

