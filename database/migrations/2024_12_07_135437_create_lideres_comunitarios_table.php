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
        Schema::create('lideres_comunitarios', function (Blueprint $table) {
            $table->bigIncrements('id_lider');
            $table->unsignedBigInteger('id_persona');
            $table->unsignedBigInteger('id_comunidad');
            $table->boolean('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lideres_comunitarios');
    }
};
