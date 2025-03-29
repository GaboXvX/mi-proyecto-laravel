<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoAutorizado extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_empleado_autorizado';
    protected $table = 'empleados_autorizados'; // Nombre de la tabla en la base de datos
    protected $fillable = [
        'id_empleado_autorizado',
        'id_cargo',
        'id_departamento',
        'nombre',
        'apellido',
        'cedula',
        'genero',
        'fecha_nacimiento',
        'altura',
        'telefono',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_empleado_autorizado'); // Relación con el modelo User
    }
}
