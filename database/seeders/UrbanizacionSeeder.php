<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Urbanizacion;

class UrbanizacionSeeder extends Seeder
{
    public function run()
    {
        $urbanizaciones = [
            ['nombre' => 'La Llanada', 'id_parroquia' => 1],
            ['nombre' => 'Las Acacias', 'id_parroquia' => 2],
            ['nombre' => 'El Bosque', 'id_parroquia' => 3],
        ];

        foreach ($urbanizaciones as $urbanizacion) {
            Urbanizacion::create($urbanizacion);
        }
    }
}
