<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LideresComunitariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lideres_comunitarios')->insert([
            [
                'id_usuario' => 1,
                'id_comunidad' => 1,
                'id_direccion' => 1,
                'slug' => Str::slug('Juan Perez'),
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'cedula' => '12345678',
                'telefono' => '0412345678',
                'correo' => 'juanperez@example.com',
                'estado' => 'activo', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_usuario' => 1,
                'id_comunidad' => 2,
                'id_direccion' => 2,
                'slug' => Str::slug('Maria Gomez'),
                'nombre' => 'Maria',
                'apellido' => 'Gomez',
                'cedula' => '87654321',
                'telefono' => '0422345678',
                'correo' => 'mariagomez@example.com',
                'estado' => 'activo', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
        
    }
}
