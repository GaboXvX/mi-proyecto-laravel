<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoriaPersona extends Model
{
    use HasFactory;
    protected $table='categorias_personas';
    protected $primaryKey='id_categoria_persona';

    public function personas()
    {
        return $this->hasMany(Persona::class, 'id_categoriaPersona');
    }
}
