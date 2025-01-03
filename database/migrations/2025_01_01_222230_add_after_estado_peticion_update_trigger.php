<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAfterEstadoPeticionUpdateTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear el trigger
        DB::unprepared('
            CREATE TRIGGER after_estado_peticion_update
            AFTER UPDATE ON peticiones
            FOR EACH ROW
            BEGIN
                IF NEW.estado_peticion = "aceptado" THEN
                    INSERT INTO users (
                        id_rol, slug, nombre, cedula, apellido, email, 
                        nombre_usuario, password, estado, created_at, updated_at
                    )
                    VALUES (
                        NEW.id_rol, NEW.slug, NEW.nombre, NEW.cedula, NEW.apellido, NEW.email, 
                        NEW.nombre_usuario, NEW.password, "activo", NEW.created_at, NEW.updated_at
                    );
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar el trigger si existe
        DB::unprepared('DROP TRIGGER IF EXISTS after_estado_peticion_update');
    }
}
