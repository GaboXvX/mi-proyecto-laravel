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
         Schema::create('instituciones_apoyo_incidencias', function (Blueprint $table) {
            $table->bigIncrements('id_institucion_apoyo');
            $table->unsignedBigInteger('id_incidencia')->nullable();
           $table->unsignedBigInteger('id_institucion')->nullable();
           $table->unsignedBigInteger('id_institucion_estacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
