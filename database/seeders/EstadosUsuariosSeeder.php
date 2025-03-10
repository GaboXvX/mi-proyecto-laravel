<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosUsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estados_usuarios')->insert([
            ['id_estado_usuario' => 1, 'nombre_estado' => 'Aceptado'],
            ['id_estado_usuario' => 2, 'nombre_estado' => 'Desactivado'],
            ['id_estado_usuario' => 3, 'nombre_estado' => 'No Verificado'],
            ['id_estado_usuario' => 4, 'nombre_estado' => 'Rechazado'],
        ]);
    }
}
