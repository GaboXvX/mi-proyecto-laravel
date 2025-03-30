<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentosSeeder extends Seeder
{
    /**
     * Ejecutar las semillas de la base de datos.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departamentos')->insert([
            [
                'nombre_departamento' => 'Agua Potable y Saneamiento',  // Primer departamento
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_departamento' => 'Salud',  // Segundo departamento
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_departamento' => 'Educación',  // Tercer departamento
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_departamento' => 'Transporte',  // Cuarto departamento
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Puedes agregar más departamentos si lo deseas
        ]);
    }
}
