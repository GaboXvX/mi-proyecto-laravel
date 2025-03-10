<?php
namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
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
                'genero' => 'M', // Se puede modificar según el caso
                'fecha_nacimiento' => Carbon::createFromFormat('Y-m-d', '2004-04-05'), // Cambiar el formato
                'altura' => '1.78',
                'password' => bcrypt('12345678'),
                'nombre_usuario' => 'admin',
                'id_estado_usuario' => 1,
                'id_rol' => 1,
                
            ],
            
        ];

        foreach ($usuarios as $usuario) {
            User::create($usuario);
        }
    }
}