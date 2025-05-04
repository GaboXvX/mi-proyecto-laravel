<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipioSeeder extends Seeder
{
    public function run()
    {
        DB::table('municipios')->insert([
          [ 'nombre' => 'Sucre',  'id_estado' => 1, ],
            [ 'nombre' => 'Andrés Eloy Blanco',  'id_estado' => 1, ],
            
            
        ]); 
}
}