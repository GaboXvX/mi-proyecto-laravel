<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaDeSeguridad extends Model
{
    protected $table = 'respuestas_de_seguridad';  // Nombre de la tabla
    protected $primaryKey = 'id_respuesta';
    protected $fillable = [
        'id_usuario',
        'id_pregunta',
        'respuesta',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Relación con la pregunta de seguridad
    public function pregunta()
    {
        return $this->belongsTo(pregunta::class, 'id_pregunta');
    }
}
