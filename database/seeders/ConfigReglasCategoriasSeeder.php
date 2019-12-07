<?php

namespace Database\Seeders;

use App\Models\categoriaPersona;
use App\Models\ConfigReglaCategoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigReglasCategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   // database/seeders/ConfigReglasCategoriasSeeder.php
public function run()
{
    $categoria = categoriaPersona::where('nombre_categoria','Líder Comunitario')->first();
    
   
        ConfigReglaCategoria::create([
            'id_categoria_persona' => $categoria->id_categoria_persona,
            'requiere_comunidad' => $categoria->nombre_categoria === 'Líder Comunitario',
            'unico_en_comunidad' => $categoria->nombre_categoria === 'Líder Comunitario',
            'una_categoria_por_comunidad_persona' => $categoria->nombre_categoria === 'Líder Comunitario',
            'mensaje_error' => $categoria->nombre_categoria === 'Líder Comunitario' 
                ? 'Ya existe un líder en esta comunidad' 
                : null
        ]);
    
}
}
