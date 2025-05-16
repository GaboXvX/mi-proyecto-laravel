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
        'id_domicilio',
        'slug',
        'nombre',
        'apellido',
        'cedula',
        'correo',
        'telefono',
        'id_categoria_persona',
    ];

    public $timestamps = true;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function incidencia()
    {
        return $this->hasMany(incidencia::class, 'id_persona');
    }

   

    public function domicilios()
{
    return $this->hasMany(Domicilio::class, 'id_persona');
}
    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function movimiento()
    {
        return $this->hasMany(movimiento::class, 'id_persona');
    }

   // Modelo Persona

}
