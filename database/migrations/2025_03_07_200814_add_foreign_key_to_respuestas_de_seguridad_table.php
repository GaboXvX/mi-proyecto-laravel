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
        Schema::table('respuestas_de_seguridad', function (Blueprint $table) {
            $table->foreign('id_usuario') ->references('id_usuario')  ->on('users')  ->onDelete('cascade');
            $table->foreign('id_pregunta') ->references('id_pregunta')  ->on('preguntas_de_seguridad')  ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respuestas_de_seguridad', function (Blueprint $table) {
            //
        });
    }
};
