<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_urbanizaciones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrbanizacionesTable extends Migration
{
    public function up()
    {
        Schema::create('urbanizaciones', function (Blueprint $table) {
            $table->bigIncrements('id_urbanizacion');  // Se crea la columna id como clave primaria
           $table->unsignedBigInteger('id_parroquia');
            $table->string('nombre');  // Nombre de la urbanización
            $table->foreign('id_parroquia')
            ->references('id_parroquia')
            ->on('parroquias')
            ->onDelete('cascade');
            $table->timestamps();  // Campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('urbanizaciones');
    }
}

