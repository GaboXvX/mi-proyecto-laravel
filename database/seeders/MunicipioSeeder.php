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
            [ 'nombre' => 'Antonio Díaz',  'id_estado' => 1, ],
            [ 'nombre' => 'Arismendi',  'id_estado' => 1, ],
            [ 'nombre' => 'Benítez',  'id_estado' => 1, ],
            [ 'nombre' => 'Bolívar',  'id_estado' => 1, ],
            [ 'nombre' => 'Cajigal',  'id_estado' => 1, ],
            [ 'nombre' => 'Cruz Salmerón Acosta',  'id_estado' => 1, ],
            [ 'nombre' => 'Libertador',  'id_estado' => 1, ],
            [ 'nombre' => 'Mariño',  'id_estado' => 1, ],
            [ 'nombre' => 'Valdez',  'id_estado' => 1, ],
            
        ]); 
}
}