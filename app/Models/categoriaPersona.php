<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoriaPersona extends Model
{
    protected $table = 'categorias_personas';
    protected $primaryKey = 'id_categoria_persona';

    protected $fillable = [
        'nombre_categoria',
        'slug',
        'descripcion'
    ];

    // Relación con reglas especiales
    public function categoriasExclusivasPersonas()
    {
        return $this->hasMany(categoriaExclusivaPersona::class, 'id_categoriaPersona');
    }

    // Relación con personas
    public function personas()
    {
        return $this->hasMany(Persona::class, 'id_categoriaPersona');
    }
    public function reglasConfiguradas()
    {
        return $this->hasOne(ConfigReglaCategoria::class, 'id_categoria_persona');
    }
}

