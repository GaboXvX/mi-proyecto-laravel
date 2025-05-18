<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitucionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instituciones = [
            ['nombre' => 'Hidrocaribe', 'es_propietario'=>0],
            ['nombre' => 'Hidrollanos', 'es_propietario'=>0],
            ['nombre' => 'INASA', 'es_propietario'=>0],
            ['nombre' => 'Hidrofalcon', 'es_propietario'=>0],
            ['nombre' => 'Hidrolara', 'es_propietario'=>0],
            ['nombre'=>'Minaguas', 'es_propietario'=>1],
        ];

        DB::table('instituciones')->insert($instituciones);
    }
}