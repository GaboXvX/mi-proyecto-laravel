<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasFactory;

    protected $table = 'instituciones';
    protected $primaryKey = 'id_institucion';
    protected $fillable = ['nombre'];

    /**
     * Relación uno a muchos con InstitucionesEstaciones.
     */
    public function estaciones()
    {
        return $this->hasMany(InstitucionEstacion::class, 'id_institucion', 'id_institucion');
    }
    public function incidencias_personas()
{
    return $this->hasMany(incidencia::class, 'id_institucion', 'id_institucion');
}
}