<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpleadosAutorizadosSeeder extends Seeder
{
    /**
     * Ejecutar las semillas de la base de datos.
     *
     * @return void
     */
    public function run()
    {
        DB::table('empleados_autorizados')->insert([
            [
                'id_cargo' => 1,
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'cedula' => 12345678,
                'genero' => 'M',
                'telefono' => '1234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_cargo' => 2,
                'nombre' => 'Ana',
                'apellido' => 'Gómez',
                'cedula' => 23456789,
                'genero' => 'F',
                'telefono' => '0987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_cargo' => 3, // Cambia según el id de cargo que desees
                'nombre' => 'Carlos',
                'apellido' => 'Lopez',
                'cedula' => 34567890,
                'genero' => 'Masculino',
               
                'telefono' => '1122334455',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Puedes agregar más registros aquí según lo necesites
        ]);
    }
}
