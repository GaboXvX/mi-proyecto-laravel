<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoEmpleadosAutorizadosSeeder extends Seeder
{
    /**
     * Ejecutar las semillas de la base de datos.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_empleados_autorizados')->insert([
            [
                'nombre_cargo' => 'Gerente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_cargo' => 'Supervisor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_cargo' => 'Auxiliar Administrativo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_cargo' => 'Secretaria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Puedes agregar más cargos si lo deseas
        ]);
    }
}
