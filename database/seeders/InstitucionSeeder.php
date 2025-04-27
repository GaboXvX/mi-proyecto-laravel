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
            ['nombre' => 'Hidrocaribe'],
            ['nombre' => 'Hidrollanos'],
            ['nombre' => 'INASA'],
            ['nombre' => 'Hidrofalcon'],
            ['nombre' => 'Hidrolara'],
        ];

        DB::table('instituciones')->insert($instituciones);
    }
}