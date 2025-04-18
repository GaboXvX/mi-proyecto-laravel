<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direcciones';
    protected $primaryKey = 'id_direccion';

    public function persona()
    {
        return $this->belongsTo(persona::class, 'id_persona');
    }

    public function lider()
    {
        return $this->hasOne(persona::class, 'id_direccion');
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class, 'id_comunidad');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'id_sector');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'id_parroquia');
    }

    public function urbanizacion()
    {
        return $this->belongsTo(Urbanizacion::class, 'id_urbanizacion');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado'); 
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'id_direccion');
    }
}
