<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class empleado extends Model
{
    use HasFactory;
    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';
    protected $fillable = [
        'slug',
        'nombre',
        'apellido',
        'cedula',
        'nombre_usuario',
        'clave',
        'correo',
        'estado',
    ];
}
