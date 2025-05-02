<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pregunta extends Model
{
    use HasFactory;
    protected $primaryKey='id_pregunta';
    protected $table='preguntas_de_seguridad';

    public function usuario(){
        return $this->belongsTo(User::class,'id_usuario');
    }
    public function respuestas()
    {
        return $this->hasMany(RespuestaDeSeguridad::class, 'id_pregunta');
    }
}
