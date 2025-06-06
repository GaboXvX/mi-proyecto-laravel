<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RespuestasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        $respuestas_de_seguridad = [
            ['id_pregunta' => 1, 'id_usuario' => 1, 'respuesta' => \Illuminate\Support\Facades\Hash::make('Firulais')],
            ['id_pregunta' => 2, 'id_usuario' => 1, 'respuesta' => \Illuminate\Support\Facades\Hash::make('Madrid')],
            ['id_pregunta' => 3, 'id_usuario' => 1, 'respuesta' => \Illuminate\Support\Facades\Hash::make('Carlos')],
        ];

        DB::table('respuestas_de_seguridad')->insert($respuestas_de_seguridad);
    }
}
