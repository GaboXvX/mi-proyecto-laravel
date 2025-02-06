<?php


namespace Database\Seeders;

use App\Models\Pregunta; 
use Illuminate\Database\Seeder;

class PreguntaSeguridadSeeder extends Seeder
{
    public function run()
    {
        $preguntas = [
            [
                'primera_mascota' => 'pepe',
                'ciudad_de_nacimiento' => 'Caracas', 
                'nombre_de_mejor_amigo' => 'Juan', 
                'created_at' => now(), 
                'updated_at' => now(),
            ],
            [
                'primera_mascota' => 'luna',
                'ciudad_de_nacimiento' => 'Cumaná',
                'nombre_de_mejor_amigo' => 'Carlos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'primera_mascota' => 'max',
                'ciudad_de_nacimiento' => 'Valencia',
                'nombre_de_mejor_amigo' => 'Ana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($preguntas as $pregunta) {
            Pregunta::create($pregunta);
        }
    }
}