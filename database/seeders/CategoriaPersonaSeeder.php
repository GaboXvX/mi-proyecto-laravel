<?php

namespace Database\Seeders;

use App\Models\categoriaPersona;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class categoriaPersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias_personas=[
        ['nombre_categoria'=>'Regular'],
        ['nombre_categoria'=>'Lider']];
        foreach ($categorias_personas as $categoria) {
            categoriaPersona::create($categoria);
        }
    }
}
