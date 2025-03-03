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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->bigIncrements('id_direccion')->unsigned(); // Clave primaria
            $table->unsignedBigInteger('id_persona');
            $table->string('calle');
            $table->string('manzana');
            $table->integer('numero_de_casa');
            $table->timestamps();

          
            $table->bigInteger('id_parroquia')->unsigned()->nullable();
            $table->bigInteger('id_urbanizacion')->unsigned()->nullable();
            $table->bigInteger('id_sector')->unsigned()->nullable();
            $table->bigInteger('id_comunidad')->unsigned()->nullable();

        $table->foreign('id_persona')->references('id_persona')->on('personas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_parroquia')->references('id_parroquia')->on('parroquias')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('id_urbanizacion')->references('id_urbanizacion')->on('urbanizaciones')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('id_sector')->references('id_sector')->on('sectores')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('id_comunidad')->references('id_comunidad')->on('comunidades')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};

