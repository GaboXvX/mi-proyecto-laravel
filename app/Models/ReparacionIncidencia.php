<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReparacionIncidencia extends Model
{
    use HasFactory;

    protected $table = 'reparaciones_incidencias';
    protected $primaryKey = 'id_reparacion'; // Cambia esto si tu clave primaria es diferente
    protected $fillable = [
        'id_incidencia_p',
        'id_incidencia_g',
        'descripcion',
        'prueba_fotografica',
        'slug',
        'id_usuario',
    ];

    // Relación con incidencia_persona
    public function incidenciaPersona()
    {
        return $this->belongsTo(incidencia_persona::class, 'id_incidencia', 'id_incidencia_p');
    }

    // Relación con IncidenciaGeneral
    public function incidenciaGeneral()
    {
        return $this->belongsTo(IncidenciaGeneral::class, 'id_incidencia', 'id_incidencia_g');
    }

    // Relación con el usuario que atendió la incidencia
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}