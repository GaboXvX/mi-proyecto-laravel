<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domicilio extends Model
{
    use HasFactory;

    protected $table = 'domicilios';
    protected $primaryKey = 'id_domicilio';
    protected $fillable = [
        'calle',
        'manzana',
        'numero_de_casa',
    ];

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }

    public function personas()
    {
        return $this->hasMany(Persona::class, 'id_domicilio');
    }
    public function Lider_Comunitario()
    {
        return $this->hasMany(Lider_Comunitario::class, 'id_domicilio');
    }
}
