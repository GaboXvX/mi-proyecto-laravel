<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';

   // En tu modelo Notificacion
   protected $fillable = [
    'id_usuario',
    'titulo',
    'mensaje',
    'tipo_notificacion',
    'mostrar_a_todos' // Quitar 'leido' ya que no existe en esta tabla
];

    

    // Relación con usuario
    public function usuarios()
{
    return $this->belongsToMany(User::class, 'notificaciones_usuarios', 'id_notificacion', 'id_usuario')
        ->withPivot('leido', 'fecha_leido')
        ->withTimestamps();
}


    // Relación con persona (si aplica)
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    // Relación con incidencia (si aplica)
    public function incidencia()
    {
        return $this->belongsTo(Incidencia::class, 'id_incidencia', 'id_incidencia');
    }
}