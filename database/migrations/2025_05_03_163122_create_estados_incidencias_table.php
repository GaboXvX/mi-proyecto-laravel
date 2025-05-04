<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estados_incidencias', function (Blueprint $table) {
            $table->bigIncrements('id_estado_incidencia');
            $table->string('nombre', 50);
            $table->string('color', 20)->nullable();
            $table->timestamps();
        });
    
        // Insertar datos iniciales con colores
        DB::table('estados_incidencias')->insert([
            ['nombre' => 'pendiente', 'color' => '#FFC107'], // Amarillo (advertencia)
            ['nombre' => 'atendido', 'color' => '#28A745'],  // Verde (éxito)
            ['nombre' => 'retrasado', 'color' => '#DC3545'], // Rojo (peligro/urgencia)
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_incidencias');
    }
};
