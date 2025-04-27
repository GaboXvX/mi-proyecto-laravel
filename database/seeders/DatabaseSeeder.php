<?php

// database/seeders/DatabaseSeeder.php

use Database\Seeders\CargoEmpleadosAutorizadosSeeder;
use Database\Seeders\categoriaPersonaSeeder;
use Database\Seeders\ParroquiaSeeder;
use Database\Seeders\UrbanizacionSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\ComunidadSeeder;
use Database\Seeders\ConfigReglasCategoriasSeeder;
use Database\Seeders\DepartamentosSeeder;
use Database\Seeders\EmpleadosAutorizadosSeeder;
use Database\Seeders\EstadoSeeder;
use Database\Seeders\EstadosUsuariosSeeder;
use Database\Seeders\InstitucionEstacionSeeder;
use Database\Seeders\InstitucionSeeder;
use Database\Seeders\MunicipioSeeder;
use Database\Seeders\PreguntaSeguridadSeeder;
use Database\Seeders\RespuestasSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UsuarioSeeder;
use PSpell\Config;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            EstadoSeeder::class,
            MunicipioSeeder::class,
            ParroquiaSeeder::class,
            UrbanizacionSeeder::class,
            SectorSeeder::class,
            ComunidadSeeder::class,
            PreguntaSeguridadSeeder::class,
            EstadosUsuariosSeeder::class,
            CargoEmpleadosAutorizadosSeeder::class,
            EmpleadosAutorizadosSeeder::class,
            UsuarioSeeder::class,
            categoriaPersonaSeeder::class,
            RespuestasSeeder::class,
            ConfigReglasCategoriasSeeder::class,
            InstitucionSeeder::class,
            InstitucionEstacionSeeder::class,
        ]);
    }
}
