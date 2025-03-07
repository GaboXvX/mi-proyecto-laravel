<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreguntaSeguridadSeeder extends Seeder
{
    public function run()
    {
        $preguntas = [
            ['pregunta' => '¿Cuál es el nombre de tu primera mascota?'],
            ['pregunta' => '¿En qué ciudad naciste?'],
            ['pregunta' => '¿Cuál es el nombre de tu mejor amigo de la infancia?'],
            ['pregunta' => '¿Cuál es tu comida favorita?'],
            ['pregunta' => '¿Cuál es el nombre de tu escuela primaria?'],
            ['pregunta' => '¿Cuál es el nombre de tu primer maestro?'],
            ['pregunta' => '¿Cuál es tu película favorita?'],
            ['pregunta' => '¿Cuál es tu libro favorito?'],
            ['pregunta' => '¿Cuál es el nombre de tu primer jefe?'],
            ['pregunta' => '¿Cuál es el nombre de tu primer amor?'],
        ];

        foreach ($preguntas as $pregunta) {
            DB::table('preguntas_de_seguridad')->insert($pregunta);
        }
    }
}
