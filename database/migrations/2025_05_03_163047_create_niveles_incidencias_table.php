<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('niveles_incidencias', function (Blueprint $table) {
            $table->bigIncrements('id_nivel_incidencia'); // Más corto que bigIncrements
            $table->tinyInteger('nivel')->unsigned()->unique(); // 1-5
            $table->string('nombre', 30); // "Crítico", "Alto", "Medio", etc.
            $table->string('descripcion', 200);
            
            // Tiempos de vencimiento en HORAS (para facilitar cálculos)
            $table->integer('horas_vencimiento')->unsigned(); 
            // Ej: Nivel 1-3 = 72h, Nivel 4-5 = 168h (7 días)
            
            // Frecuencia de recordatorios (en horas)
            $table->integer('frecuencia_recordatorio')->unsigned()->default(24); // Recordar cada 24h
            
            // Configuración de colores y estado
            $table->string('color', 7)->default('#FF0000'); // Rojo por defecto
            $table->boolean('activo')->default(true);
            
            $table->timestamps();
        });

        // Insertar datos iniciales (opcional)
        DB::table('niveles_incidencias')->insert([
            ['nivel' => 1, 'nombre' => 'Mínimo',    'descripcion' => 'Impacto muy bajo',  'horas_vencimiento' => 72, 'color' => '#2ECC71'],
            ['nivel' => 2, 'nombre' => 'Bajo',      'descripcion' => 'Impacto limitado',  'horas_vencimiento' => 72, 'color' => '#3498DB'],
            ['nivel' => 3, 'nombre' => 'Medio',     'descripcion' => 'Impacto moderado',  'horas_vencimiento' => 72,  'color' => '#F39C12'],
            ['nivel' => 4, 'nombre' => 'Alto',      'descripcion' => 'Impacto significativo', 'horas_vencimiento' => 168,  'color' => '#FF5733'],
            ['nivel' => 5, 'nombre' => 'Crítico',   'descripcion' => 'Impacto alto en operaciones', 'horas_vencimiento' => 168,  'color' => '#FF0000']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niveles_incidencias');
    }
};