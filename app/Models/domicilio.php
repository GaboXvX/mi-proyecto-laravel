<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domicilio extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'domicilios';

    // Clave primaria
    protected $primaryKey = 'id_domicilio';

    // Campos asignables en masa
    protected $fillable = [
        'id_persona',
        'id_estado',
        'id_municipio',
        'id_parroquia',
        'id_urbanizacion',
        'id_sector',
        'id_comunidad',
        'calle',
        'manzana',
        'bloque',
        'numero_de_vivienda',
        'es_principal',
    ];

    // Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'id_parroquia', 'id_parroquia');
    }

    public function urbanizacion()
    {
        return $this->belongsTo(Urbanizacion::class, 'id_urbanizacion', 'id_urbanizacion');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'id_sector', 'id_sector');
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class, 'id_comunidad', 'id_comunidad');
    }
    
}
