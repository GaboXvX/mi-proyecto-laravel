<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PruebaFotografica extends Model
{
    use HasFactory;
    protected $table = 'pruebas_fotograficas';
    protected $primaryKey = 'id_prueba_fotografica';
    protected $fillable = [
        'id_incidencia',
        'id_reparacion',
        'observacion',
        'ruta',
        'etapa_foto',
        'created_at',
        'updated_at'
    ];
    /**
     * Relación con el modelo Incidencia.
     */
    public function incidencia()
    {
        return $this->belongsTo(Incidencia::class, 'id_incidencia', 'id_incidencia');
    }
    /**
     * Relación con el modelo ReparacionIncidencia.
     */
    public function reparacion()
{
    return $this->belongsTo(ReparacionIncidencia::class, 'id_reparacion');
}
}
