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
<<<<<<< HEAD

=======
public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }
>>>>>>> 6274081162731933fa5a1f461cf7cde9adc29d56
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
}