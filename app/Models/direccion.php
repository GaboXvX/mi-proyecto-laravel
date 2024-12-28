<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direccion';
    protected $primaryKey = 'id_direccion';

    protected $fillable = [
        'estado', 
        'municipio', 
        'comunidad', 
        'sector',
    ];

    public function domicilios()
    {
        return $this->hasMany(Domicilio::class, 'id_direccion');
    }
    public function persona()
    {
        return $this->hasMany(persona::class, 'id_direccion');
    }
    public function lider()
    {
        return $this->hasOne(Lider_Comunitario::class, 'id_direccion');
    }
    
}
