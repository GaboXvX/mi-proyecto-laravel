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
        Schema::table('peticiones', function (Blueprint $table) {
            $table->foreign('id_usuario')->nullable() ->references('id_usuario')  ->on('users')  ->onDelete('cascade');
            $table->foreign('id_rol')->nullable() ->references('id_rol')  ->on('roles')  ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peticiones', function (Blueprint $table) {
            //
        });
    }
};
