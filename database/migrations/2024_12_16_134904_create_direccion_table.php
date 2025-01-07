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
        Schema::create('direccion', function (Blueprint $table) {
            $table->bigIncrements('id_direccion');
            $table->string('estado');
            $table->string('municipio');
            $table->string('parroquia');
           $table->string('urbanizacion');
            $table->string('sector');
            $table->string('comunidad');
            $table->string('calle'); 
            $table->string('manzana');
            $table->integer('numero_de_casa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direccion');
    }
};
