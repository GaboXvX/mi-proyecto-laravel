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
        Schema::table('direcciones_incidencias', function (Blueprint $table) {
            $table->foreign('id_parroquia')->references('id_parroquia')->on('parroquias')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('id_urbanizacion')->references('id_urbanizacion')->on('urbanizaciones')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('id_sector')->references('id_sector')->on('sectores')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('id_comunidad')->references('id_comunidad')->on('comunidades')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('id_estado') ->references('id_estado')  ->on('estados')  ->onDelete('cascade');
            $table->foreign('id_municipio') ->references('id_municipio')  ->on('municipios')  ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domicilios', function (Blueprint $table) {
            //
        });
    }
};
