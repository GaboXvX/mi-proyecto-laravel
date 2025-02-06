<?php

// database/seeders/PreguntaSeguridadSeeder.php
namespace Database\Seeders;

use App\Models\Pregunta; // Asegúrate de que el nombre del modelo esté correctamente escrito
use Illuminate\Database\Seeder;

class PreguntaSeguridadSeeder extends Seeder
{
    public function run()
    {
        // Definir los datos completos para cada registro
        $preguntas = [
            [
                'primera_mascota' => 'pepe',
                'ciudad_de_nacimiento' => 'Caracas', // Proporciona un valor para este campo
                'nombre_de_mejor_amigo' => 'Juan', // Proporciona un valor para este campo
                'created_at' => now(), // Opcional: incluir marcas de tiempo
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

        // Insertar los registros en la base de datos
        foreach ($preguntas as $pregunta) {
            Pregunta::create($pregunta);
        }
    }
}