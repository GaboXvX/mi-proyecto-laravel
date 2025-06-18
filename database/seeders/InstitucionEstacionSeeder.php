<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitucionEstacionSeeder extends Seeder
{
    public function run(): void
    {
        $estaciones = [
            [
                'id_institucion' => 1,
                'id_estado' => 1,  // <- QUITÉ EL ESPACIO DESPUÉS DE id_estado
                'id_municipio' => 1, 
                'nombre' => 'Unidad de Atencion 1 - Hidrocaribe', 
                'codigo_estacion' => 'HC001'
            ],
            [
                'id_institucion' => 1,
                'id_estado' => 1,  // <- QUITÉ EL ESPACIO DESPUÉS DE id_estado
                'id_municipio' => 2, 
                'nombre' => 'Unidad de Atención 2 - Hidrocaribe', 
                'codigo_estacion' => 'HC002'
            ],
        ];

        DB::table('instituciones_estaciones')->insert($estaciones);
    }
}