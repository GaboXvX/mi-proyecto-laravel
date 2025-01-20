<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class, 'id_comunidad');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'id_usuario');
    }
    public function movimiento(){
        return $this->hasMany(movimiento::class,'id_lider');
    }
    public function direccion(){
        return $this->belongsTo(direccion::class,'id_direccion');
    }
}