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
        Schema::table('lider_comunitario', function (Blueprint $table) {
              $table->foreign('id_direccion') ->references('id_direccion')  ->on('direccion')  ->onDelete('cascade');
              $table->foreign('id_usuario') ->references('id_usuario')  ->on('users')  ->onDelete('cascade');
              $table->foreign('id_domicilio')->references('id_domicilio')->on('domicilios')->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lider_comunitario', function (Blueprint $table) {
            $table->dropColumn('id_direccion');
        });
    }
};
