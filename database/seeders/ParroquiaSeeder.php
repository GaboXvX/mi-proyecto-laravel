<?php

namespace Database\Seeders;

use App\Models\Parroquia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParroquiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $parroquias = [
            ['nombre' => 'Altagracia', 'id_parroquia' => '1'],
            ['nombre' => 'Valentin Valiente', 'id_parroquia' => '2'],
            ['nombre' => 'San Juan', 'id_parroquia' => '3'],
        ];

        foreach ($parroquias as $parroquia) {
            Parroquia::create($parroquia);
        }
    }
}
