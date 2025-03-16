<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipioSeeder extends Seeder
{
    public function run()
    {
        DB::table('municipios')->insert([
            'nombre' => 'Sucre',  // Nombre del municipio
            'id_estado' => 1,     // id_estado 2 = Sucre
        ]); 
}
}