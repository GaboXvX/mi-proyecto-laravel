<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class institucionApoyo extends Model
{
    use HasFactory;
    protected $table = 'instituciones_apoyo_incidencias';
    protected $primaryKey = 'id_institucion_apoyo';
    protected $fillable = [
        'id_institucion_apoyo',
        'id_incidencia',
        'id_institucion',
        'id_institucion_estacion'
    ];
    /**
     * Relación uno a muchos con Incidencia.
     */
    public function incidencia()
    {
        return $this->belongsTo(incidencia::class, 'id_incidencia', 'id_incidencia');
    }
    /**
     * Relación uno a muchos con Institucion.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'id_institucion', 'id_institucion');
    }
    /**
     * Relación uno a muchos con InstitucionEstacion.
     */
    public function Estacion()
    {
        return $this->belongsTo(InstitucionEstacion::class, 'id_institucion_estacion', 'id_institucion_estacion');
    }
}
