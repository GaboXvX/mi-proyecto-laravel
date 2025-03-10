<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoUsuario extends Model
{
    use HasFactory;

    // Definir la tabla que se va a utilizar
    protected $table = 'estados_usuarios'; // Asegúrate de que el nombre de la tabla sea correcto
protected $primaryKey='id_estado_usuario';
    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'nombre_estado',  // Esto será el nombre del estado (ej. 'Verificado', 'No verificado')
    ];

    // Relación con el modelo User
    public function users()
    {
        return $this->hasMany(User::class, 'id_estado_usuario');
    }
}
