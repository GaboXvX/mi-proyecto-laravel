<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    public function run()
    {
        // Crear los estados
        $estados = [
            ['nombre' => 'Sucre'],
            
        ];

        // Insertar los estados en la tabla 'estados'
        foreach ($estados as $estado) {
            DB::table('estados')->insert($estado);
        }
    }
}

