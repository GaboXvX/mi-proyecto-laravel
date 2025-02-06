<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DireccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insertar 10 direcciones de ejemplo (ajusta la cantidad según necesites)
        DB::table('direcciones')->insert([
            [
                'estado' => 'Sucre',
                'municipio' => 'Sucre',
                'calle' => 'Calle 1',
                'manzana' => 'A',
                'numero_de_casa' => '123',
                'id_parroquia' => 1, // Asegúrate de que los ids de parroquia, urbanización, sector y comunidad existan
                'id_urbanizacion' => 1,
                'id_sector' => 1,
                'id_comunidad' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'estado' => 'Miranda',
                'municipio' => 'Carrizal',
                'calle' => 'Calle 2',
                'manzana' => 'B',
                'numero_de_casa' => '456',
                'id_parroquia' => 2,
                'id_urbanizacion' => 2,
                'id_sector' => 2,
                'id_comunidad' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Agrega más direcciones según sea necesario
        ]);
    }
}
