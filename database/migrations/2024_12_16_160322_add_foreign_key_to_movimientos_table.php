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
        Schema::table('movimientos', function (Blueprint $table) {
           $table->foreign('id_usuario') ->references('id_usuario')  ->on('users')  ->onDelete('cascade');
            $table->foreign('id_persona') ->references('id_persona')  ->on('personas')  ->onDelete('cascade');
            $table->foreign('id_incidencia') ->references('id_incidencia')  ->on('incidencias')  ->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn('id_usuario');
            $table->dropColumn('id_persona');
            $table->dropColumn('id_incidencia');
        });
    }
};
