<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run()
    {
        $sectores = [
            ['nombre' => 'Sector A', 'id_urbanizacion' => 1],
            ['nombre' => 'Sector B', 'id_urbanizacion' => 2],
            ['nombre' => 'Sector C', 'id_urbanizacion' => 3],
        ];

        foreach ($sectores as $sector) {
            Sector::create($sector);
        }
    }
}
