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
        'id_incidencia',
        'descripcion',
        'prueba_fotografica',
        'slug',
        'id_usuario',
    ];

    // Relación con incidencia_persona
    public function incidencia()
    {
        return $this->belongsTo(incidencia::class, 'id_incidencia', 'id_incidencia');
    }

    // Relación con IncidenciaGeneral
   

    // Relación con el usuario que atendió la incidencia
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    // Relación con el personal de reparación
    public function personalReparacion()
    {
        return $this->belongsTo(personalReparacion::class, 'id_personal_reparacion');
    }
}