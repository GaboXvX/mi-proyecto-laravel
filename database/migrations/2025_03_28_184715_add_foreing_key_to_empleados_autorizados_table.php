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
        Schema::table('empleados_autorizados', function (Blueprint $table) {
            $table->foreign('id_cargo')->references('id_cargo')->on('cargo_empleados_autorizados')->onDelete('cascade');
            $table->foreign('id_departamento')->references('id_departamento')->on('departamentos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados_autorizados', function (Blueprint $table) {
            //
        });
    }
};
