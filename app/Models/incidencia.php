<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class incidencia extends Model
{
    use HasFactory;

    protected $table = 'incidencias';
    protected $primaryKey = 'id_incidencia';

    protected $fillable = [
        'id_persona',
        'id_categoria_exclusiva', // Relación con categorias_exclusivas_personas
        'id_direccion',
        'id_usuario',
        'slug',
        'tipo_incidencia',
        'descripcion',
        'nivel_prioridad',
        'id_institucion',
        'id_institucion_estacion',
        'estado',
        'created_at',
        'updated_at'
    ];

    /**
     * Usar el campo 'slug' como clave de ruta.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Relación con el modelo Persona.
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    /**
     * Relación con el modelo categoriaExclusivaPersona.
     */
    public function categoriaExclusiva()
    {
        return $this->belongsTo(categoriaExclusivaPersona::class, 'id_categoria_exclusiva');
    }

    /**
     * Relación con el modelo Movimiento.
     */
    public function movimiento()
    {
        return $this->hasMany(movimiento::class, 'id_incidencia');
    }

    /**
     * Relación con el modelo Direccion.
     */
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }

    /**
     * Relación con el modelo User.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function institucion()
{
    return $this->belongsTo(Institucion::class, 'id_institucion');
}
public function estacion()
{
    return $this->belongsTo(InstitucionEstacion::class, 'id_institucion_estacion');
}
public function institucionEstacion()
{
    return $this->hasOne(InstitucionEstacion::class, 'id_institucion_estacion', 'id_institucion_estacion');
}
}
