<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Persona extends Model
{
    use HasFactory, HasRoles;

    protected $table = 'personas';
    protected $primaryKey = 'id_persona';

    protected $fillable = [
        'id_direccion', 
        'id_usuario', 
        'id_lider', 
        'id_domicilio',
        'slug',
        'nombre',
        'apellido',
        'cedula',
        'correo',
        'telefono',
    ];

    public $timestamps = true;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'id_persona');
    }

    public function lider_Comunitario()
    {
        return $this->hasMany(Lider_Comunitario::class, 'id_persona', 'id_persona');
    }
    

    public function direccion()
    {
        return $this->hasMany(direccion::class, 'id_persona');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'id_usuario');
    }
    public function movimiento(){
        return $this->hasMany(movimiento::class,'id_persona');
    }
}
