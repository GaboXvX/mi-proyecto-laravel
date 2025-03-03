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
            $table->foreign('id_persona') ->references('id_persona')  ->on('personas')  ->onDelete('cascade');
              $table->foreign('id_comunidad') ->references('id_comunidad')  ->on('comunidades')  ->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lideres_comunitarios', function (Blueprint $table) {
            $table->dropColumn('id_comunidad');
        });
    }
};
