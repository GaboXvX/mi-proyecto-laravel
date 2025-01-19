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
<<<<<<< HEAD
           
=======
              $table->foreign('id_direccion') ->references('id_direccion')  ->on('direccion')  ->onDelete('cascade');
>>>>>>> 6274081162731933fa5a1f461cf7cde9adc29d56
              $table->foreign('id_usuario') ->references('id_usuario')  ->on('users')  ->onDelete('cascade');
             
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
