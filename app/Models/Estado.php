<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    // La tabla asociada con el modelo (por defecto Laravel usa el plural en minúsculas)
    protected $table = 'estados';
    protected $primaryKey='id_estado';
    // Los atributos que se pueden asignar de manera masiva
    protected $fillable = ['nombre'];

    /**
     * Relación uno a muchos con el modelo Municipio
     */
    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'id_estado');
    }
    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'id_estado');  // 'id_estado' es la clave foránea en Direccion
    }
}
