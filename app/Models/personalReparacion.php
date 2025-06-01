<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class personalReparacion extends Model
{
    use HasFactory;
    protected $table = 'personal_reparaciones';
    protected $primaryKey = 'id_personal_reparacion';
    protected $fillable = [
        'id_usuario',
        'slug',
        'id_institucion',
        'id_institucion_estacion',
        'nombre',
        'apellido',
        'nacionalidad',
        'cedula',
        'telefono',
        'genero'
    ];
    /**
     * Relación uno a muchos con Incidencia.
     */
    // En tu modelo PersonalReparacion.php
public function getRouteKeyName()
{
    return 'slug'; // Esto hará que las rutas usen 'slug' en lugar de 'id'
}
    public function incidencias()
    {
        return $this->hasMany(incidencia::class, 'id_personal_reparacion', 'id_personal_reparacion');
    }
    /**
     * Relación uno a muchos con Institucion.
     */
    public function institucion()
    {
        return $this->belongsTo(institucion::class, 'id_institucion', 'id_institucion');
    }
    /**
     * Relación uno a muchos con InstitucionEstacion.
     */
    public function institucionEstacion()
    {
        return $this->belongsTo(institucionEstacion::class, 'id_institucion_estacion', 'id_institucion_estacion');
    }
    /**
     * Relación uno a muchos con Usuario.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
    /**
     * Relación uno a muchos con reparacionIncidencia.
     * 
     */
    public function reparacionIncidencias()
    {
        return $this->hasMany(reparacionIncidencia::class, 'id_personal_reparacion', 'id_personal_reparacion');
    }
   
}
