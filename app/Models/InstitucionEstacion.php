<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitucionEstacion extends Model
{
    use HasFactory;

    protected $table = 'instituciones_estaciones';
    protected $primaryKey = 'id_institucion_estacion';
    protected $fillable = ['id_institucion', 'id_municipio', 'nombre', 'codigo_estacion'];

    /**
     * Relación inversa con Institucion.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'id_institucion', 'id_institucion');
    }

    /**
     * Relación inversa con Municipio (si existe el modelo Municipio).
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }
    public function incidencias()
{
    return $this->hasMany(incidencia::class, 'id_institucion_estacion', 'id_institucion_estacion');
}
    /**
     * Relación uno a muchos con InstitucionApoyo.
     */
    public function institucionesApoyo()
    {
        return $this->hasMany(institucionApoyo::class, 'id_institucion_estacion', 'id_institucion_estacion');
    }
}