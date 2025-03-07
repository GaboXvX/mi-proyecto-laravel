<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $usuarios = [
            [
                'nombre' => 'Admin',
                'apellido' => 'Admin',
                'slug' => 'admin',
                'cedula' => '12345678',
                'email' => 'admin@example.com',
                'password' => bcrypt('12345678'),
                'nombre_usuario' => 'admin',
                'estado' => 'activo',
                'id_rol' => 1,
                
            ],
            
        ];

        foreach ($usuarios as $usuario) {
            User::create($usuario);
        }
    }
}