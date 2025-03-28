<?php

// database/seeders/DatabaseSeeder.php

use Database\Seeders\CargoEmpleadosAutorizadosSeeder;
use Database\Seeders\categoriaPersonaSeeder;
use Database\Seeders\ParroquiaSeeder;
use Database\Seeders\UrbanizacionSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\ComunidadSeeder;
use Database\Seeders\DepartamentosSeeder;
use Database\Seeders\EmpleadosAutorizadosSeeder;
use Database\Seeders\EstadoSeeder;
use Database\Seeders\EstadosUsuariosSeeder;
use Database\Seeders\MunicipioSeeder;
use Database\Seeders\RolSeeder;
use Database\Seeders\PreguntaSeguridadSeeder;
use Database\Seeders\RespuestasSeeder;
use Database\Seeders\UsuarioSeeder;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            EstadoSeeder::class,
            MunicipioSeeder::class,
            ParroquiaSeeder::class,
            UrbanizacionSeeder::class,
            SectorSeeder::class,
            ComunidadSeeder::class,
            RolSeeder::class,
            PreguntaSeguridadSeeder::class,
            EstadosUsuariosSeeder::class,
            UsuarioSeeder::class,
            categoriaPersonaSeeder::class,
            RespuestasSeeder::class,
            DepartamentosSeeder::class,
            CargoEmpleadosAutorizadosSeeder::class,
            EmpleadosAutorizadosSeeder::class,

        ]);
    }
}
