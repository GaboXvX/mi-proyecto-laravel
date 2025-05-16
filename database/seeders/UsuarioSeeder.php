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
                'nombre_usuario' => 'admin',

                'email' => 'admin@example.com',
                'password' => bcrypt('12345678'),
                'id_estado_usuario' => 1, // Aceptado
                'id_empleado_autorizado' => 1, // Relación con empleados_autorizados
                'slug' => 'admin',
                'id_role' => 1, // Relación con roles
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::create($usuario)->assignRole('admin');
        }
    }
}