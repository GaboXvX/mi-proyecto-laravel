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
        Schema::table('lideres_comunitarios', function (Blueprint $table) {
           
              $table->foreign('id_usuario') ->references('id_usuario')  ->on('users')  ->onDelete('cascade');
              $table->foreign('id_direccion') ->references('id_direccion')  ->on('direcciones')  ->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lideres_comunitarios', function (Blueprint $table) {
            $table->dropColumn('id_direccion');
        });
    }
};
