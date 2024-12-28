<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lider_Comunitario extends Model
{
    use HasFactory;

    protected $table = 'lider_comunitario';
    protected $primaryKey = 'id_lider';

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'id_lider');
    }
    public function personas()
    {
        return $this->hasMany(Persona::class, 'id_lider');
}
public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }
    public function domicilio()
    {
        return $this->belongsTo(domicilio::class, 'id_domicilio');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'id_usuario');
    }
}