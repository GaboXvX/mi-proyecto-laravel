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
        Schema::table('direcciones', function (Blueprint $table) {
            $table->foreign('id_persona')->references('id_persona')->on('personas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_parroquia')->references('id_parroquia')->on('parroquias')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('id_urbanizacion')->references('id_urbanizacion')->on('urbanizaciones')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('id_sector')->references('id_sector')->on('sectores')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('id_comunidad')->references('id_comunidad')->on('comunidades')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('id_estado') ->references('id_estado')  ->on('estados')  ->onDelete('cascade');
            $table->foreign('id_municipio') ->references('id_municipio')  ->on('municipios')  ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direcciones', function (Blueprint $table) {
            //
        });
    }
};
