<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'cargos_empleados_autorizados';
    protected $primaryKey = 'id_cargo';

    protected $fillable = [
        'nombre_cargo',
    ];

    public function empleadosAutorizados()
    {
        return $this->hasMany(EmpleadoAutorizado::class, 'id_cargo');
    }
}
