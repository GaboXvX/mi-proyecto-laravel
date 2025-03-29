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
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('id_rol')->nullable() ->references('id_rol')  ->on('roles')  ->onDelete('cascade');
            $table->foreign('id_estado_usuario')->nullable() ->references('id_estado_usuario')  ->on('estados_usuarios')  ->onDelete('cascade');
            $table->foreign('id_empleado_autorizado')->nullable() ->references('id_empleado_autorizado')  ->on('empleados_autorizados')  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
