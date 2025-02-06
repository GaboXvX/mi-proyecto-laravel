<?php
namespace Database\Seeders;

use App\Models\Comunidad;
use Illuminate\Database\Seeder;

class ComunidadSeeder extends Seeder
{
    public function run()
    {
        $comunidades = [
            ['nombre' => 'Comunidad Los Rosales', 'id_sector' => 1],
            ['nombre' => 'Comunidad Las Margaritas', 'id_sector' => 2],
            ['nombre' => 'Comunidad Los Girasoles', 'id_sector' => 3],
        ];

        foreach ($comunidades as $comunidad) {
            Comunidad::create($comunidad);
        }
    }
}
