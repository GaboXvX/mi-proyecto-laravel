<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lider_Comunitario extends Model
{
    use HasFactory;

    protected $table ='lideres_comunitarios';
    protected $primaryKey = 'id_lider';
    protected $fillable = [
        'id_persona',  
        'estado',
        'id_comunidad'      
       
    ];
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'id_lider');
    }
    public function personas()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
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
    public function direccion()
    {
        return $this->belongsTo(Direccion::class,'id_direccion');
    }
    
}