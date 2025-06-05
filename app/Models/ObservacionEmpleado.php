<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObservacionEmpleado extends Model
{
    use HasFactory;

    protected $table = 'observaciones_empleados';
    protected $primaryKey = 'id_observacion_empleado';

    protected $fillable = [
        'id_empleado_autorizado',
        'observacion',
        'tipo'
    ];

    public function empleado()
    {
        return $this->belongsTo(EmpleadoAutorizado::class, 'id_empleado_autorizado');
    }
}