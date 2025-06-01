<?php

namespace Database\Seeders;

use App\Http\Controllers\personalController;
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
        permission::create(['name' => 'desactivar empleados'])->assignRole($role1);
        permission::create(['name' => 'ver empleados'])->assignRole($role1);
     permission::create(['name' => 'agregar empleados'])->assignRole($role1);
        permission::create(['name' => 'habilitar empleados'])->assignRole($role1);
        permission::create(['name' => 'restaurar empleados'])->assignRole($role1);
        permission::create(['name' => 'ver movimientos empleados'])->assignRole($role1);
        permission::create(['name' => 'ver grafica incidencia'])->assignRole($role1);
        permission::create(['name' => 'descargar grafica incidencia'])->assignRole($role1);
        permission::create(['name' => 'descargar listado incidencias'])->assignRole($role1);
        permission::create(['name' => 'ver niveles incidencias'])->assignRole($role1);
        permission::create(['name' => 'editar niveles incidencias'])->assignRole($role1);
        permission::create(['name' => 'agregar niveles incidencias'])->assignRole($role1);
        permission::create(['name' => 'ver instituciones'])->assignRole($role1);
        permission::create(['name' => 'editar instituciones'])->assignRole($role1);
        permission::create(['name' => 'ver movimientos'])->assignRole($role1);
        permission::create(['name' => 'descargar detalles incidencias'])->assignRole($role1);
        permission::create(['name' => 'aceptar peticion'])->assignRole($role1);
        permission::create(['name' => 'ver peticiones'])->assignRole($role1);
        permission::create(['name' => 'rechazar peticiones'])->assignRole($role1);
        permission::create(['name' => 'editar empleados'])->assignRole($role1);

    }
}
