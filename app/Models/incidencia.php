<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class incidencia extends Model
{
    use HasFactory;

    protected $table = 'incidencias';
    protected $primaryKey = 'id_incidencia';
    protected $casts = [
        'fecha_vencimiento' => 'datetime',
    ];
    
    protected $fillable = [
        'id_persona',
        'id_categoria_exclusiva', // Relación con categorias_exclusivas_personas
        'id_direccion_incidencia',
        'id_usuario',
        'slug',
        'id_tipo_incidencia',
        'descripcion',
        'id_institucion',
        'id_institucion_estacion',
        'id_estado_incidencia',
        'id_nivel_incidencia',
        'cod_incidencia',
        'fecha_vencimiento',
        'ultimo_recordatorio',
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
    public function direccionIncidencia()
    {
        return $this->belongsTo(direccionIncidencia::class, 'id_direccion_incidencia');
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
    /**
     * Relación con el modelo EstadoIncidencia.
     */
    public function estadoIncidencia()
    {
        return $this->belongsTo(EstadoIncidencia::class, 'id_estado_incidencia');
    }
    /**
     * Relación con el modelo NivelIncidencia.
     */
    public function nivelIncidencia()
    {
        return $this->belongsTo(NivelIncidencia::class, 'id_nivel_incidencia');
    }
    /**
     * Relación con el modelo CategoriaExclusivaPersona.
     */
   
    /**
     * Relación con el modelo TipoIncidencia.
     */
    public function tipoIncidencia()
    {
        return $this->belongsTo(tipoIncidencia::class, 'id_tipo_incidencia');
    }
public function personalReparacion()
{
    return $this->belongsTo(PersonalReparacion::class);
}
// Agrega esta relación al final de la clase
public function reparacion()
{
    return $this->hasOne(ReparacionIncidencia::class, 'id_incidencia', 'id_incidencia');
}
}
