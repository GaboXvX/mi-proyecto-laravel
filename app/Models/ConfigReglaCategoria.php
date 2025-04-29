<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigReglaCategoria extends Model
{
    use HasFactory;

    protected $table = 'configuraciones_reglas_categorias';
    protected $primaryKey = 'id_config';

    protected $fillable = [
        'id_categoria_persona',
        'requiere_comunidad',
        'unico_en_comunidad',
        'unico_en_sistema',
        'mensaje_error'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaPersona::class, 'id_categoria_persona');
    }
}