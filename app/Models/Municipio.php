<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    // La tabla asociada con el modelo (por defecto Laravel usa el plural en minúsculas)
    protected $table = 'municipios';
    protected $primaryKey='id_municipio';

    // Los atributos que se pueden asignar de manera masiva
    protected $fillable = ['nombre', 'id_estado'];

    /**
     * Relación muchos a uno con el modelo Estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    /**
     * Relación uno a muchos con el modelo Parroquia
     */
    public function parroquias()
    {
        return $this->hasMany(Parroquia::class, 'id_municipio');
    }
    public function direcciones()
    {
        return $this->hasMany(direccionIncidencia::class, 'id_municipio'); 
    }
}
