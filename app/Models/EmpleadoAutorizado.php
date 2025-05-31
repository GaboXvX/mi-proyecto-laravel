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
        'nombre',
        'apellido',
        'cedula',
        'genero',
        'telefono',
        
        
        
    ];

    public function usuario()
    {
        return $this->hasOne(User::class, 'id_empleado_autorizado', 'id_empleado_autorizado');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo', 'id_cargo');
    }
}
