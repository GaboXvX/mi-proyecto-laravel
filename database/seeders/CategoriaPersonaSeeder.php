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
        $categorias = [
            [
                'nombre_categoria' => 'Regular',
                'slug' => 'regular',
                'descripcion' => 'Personas sin roles especiales en el sistema'
            ],
            [
                'nombre_categoria' => 'Líder Comunitario',
                'slug' => 'lider-comunitario',
                'descripcion' => 'Representantes autorizados de comunidades'
            ],
           
        ];
    
        // 2. Verificación y creación condicional
        foreach ($categorias as $categoria) {
            categoriaPersona::firstOrCreate(
                ['slug' => $categoria['slug']], // Buscar por slug único
                $categoria // Datos a insertar si no existe
            );
        }
    }
}
