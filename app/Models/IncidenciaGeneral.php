<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidenciaGeneral extends Model
{
    use HasFactory;

    protected $table = 'incidencias_generales'; // Nombre de la tabla
    protected $primaryKey = 'id_incidencia_g'; // Clave primaria
    public $incrementing = true; // Si la clave primaria es autoincremental
    protected $keyType = 'int'; // Tipo de la clave primaria

    protected $fillable = [
        'id_direccion',
        'id_usuario',
        'id_institucion',
        'id_institucion_estacion',
        // Agrega aquí otros campos que necesites asignar masivamente
    ];

    // Relación con la tabla Direcciones
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id_direccion');
    }

    // Relación con la tabla Usuarios
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    // Relación con la tabla Instituciones
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'id_institucion', 'id_institucion');
    }

    // Relación con la tabla InstitucionesEstaciones
    public function institucionEstacion()
    {
        return $this->belongsTo(InstitucionEstacion::class, 'id_institucion_estacion', 'id_institucion_estacion');
    }
}