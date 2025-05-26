<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasFactory;

    protected $table = 'instituciones';
    protected $primaryKey = 'id_institucion';
    protected $fillable = [ 
    'nombre', 
    'logo_path', 
    'encabezado_html',
    'pie_html',
];

    /**
     * Relación uno a muchos con InstitucionesEstaciones.
     */
    public function estaciones()
    {
        return $this->hasMany(InstitucionEstacion::class, 'id_institucion', 'id_institucion');
    }
    /**
     * Relación uno a muchos con InstitucionesApoyoIncidencias.
     */
    public function institucionesApoyo()
    {
        return $this->hasMany(institucionApoyo::class, 'id_institucion', 'id_institucion');
    }
}