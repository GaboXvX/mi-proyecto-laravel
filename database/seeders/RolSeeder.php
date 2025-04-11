<?php
namespace Database\Seeders;


use App\Models\roles;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'rol' => 'admin',
                'id_rol' => 1,
            ],
            [
                'rol' => 'registrador',
                'id_rol' => 2
            ],
           
           
        ];

        foreach ($roles as $rol) {
            roles::create($rol);
        }
    }
}
