<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1=Role::create(['name' => 'admin']);
        $role2=Role::create(['name' => 'registrador']);
        permission::create(['name' => 'desactivar usuarios'])->assignRole($role1);
        permission::create(['name' => 'ver usuarios'])->assignRole($role1);
        permission::create(['name' => 'habilitar usuarios'])->assignRole($role1);
        permission::create(['name' => 'restaurar usuarios'])->assignRole($role1);
        permission::create(['name' => 'ver movimientos usuarios'])->assignRole($role1);
        permission::create(['name' => 'ver grafica incidencia'])->assignRole($role1);
        permission::create(['name' => 'cambiar estado de incidencias'])->assignRole($role1);
        permission::create(['name' => 'descargar grafica incidencia'])->assignRole($role1);
        permission::create(['name' => 'descargar listado incidencias'])->assignRole($role1);

    }
}
