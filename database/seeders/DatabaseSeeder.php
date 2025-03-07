<?php

// database/seeders/DatabaseSeeder.php

use Database\Seeders\categoriaPersonaSeeder;
use Database\Seeders\ParroquiaSeeder;
use Database\Seeders\UrbanizacionSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\ComunidadSeeder;
use Database\Seeders\RolSeeder;
use Database\Seeders\PreguntaSeguridadSeeder;
use Database\Seeders\RespuestasSeeder;
use Database\Seeders\UsuarioSeeder;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ParroquiaSeeder::class,
            UrbanizacionSeeder::class,
            SectorSeeder::class,
            ComunidadSeeder::class,
            RolSeeder::class,
            PreguntaSeguridadSeeder::class,
            UsuarioSeeder::class,
            categoriaPersonaSeeder::class,
            RespuestasSeeder::class,
        ]);
    }
}
