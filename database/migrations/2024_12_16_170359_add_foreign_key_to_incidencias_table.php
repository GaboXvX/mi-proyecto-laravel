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
        Schema::table('incidencias', function (Blueprint $table) {
          $table->foreign('id_persona') ->references('id_persona')  ->on('personas')  ->onDelete('cascade'); 
          $table->foreign('id_lider') ->references('id_lider')  ->on('lideres_comunitarios')  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropColumn('id_persona');
        });
    }
};
