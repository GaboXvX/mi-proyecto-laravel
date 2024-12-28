<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peticion extends Model
{
    use HasFactory;
    
    protected $table = 'peticiones';
    protected $primaryKey = 'id_peticion';

   
    protected $fillable = [
        'id_rol',
        'estado_peticion',
        'nombre',
        'apellido',
        'cedula',
        'email',
        'nombre_usuario',
        'password',
        'estado',
    ];
    public function rol()
    {
        return $this->belongsTo(roles::class, 'id_rol', 'id_rol'); 
    }

}
