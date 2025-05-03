<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class categoriaExclusivaPersona extends Model
{
    use HasFactory;

    protected $table = 'categorias_exclusivas_personas';
    protected $primaryKey = 'id_categoria_exclusiva';

    protected $fillable = [
        'id_persona',
        'id_categoria_persona',
        'id_comunidad',
        'tipo_regla',
        'valor_regla',
        'es_activo',
        'fecha_aprobacion',
        'aprobado_por'
    ];

    protected $casts = [
        'es_activo' => 'boolean',
        'fecha_aprobacion' => 'datetime'
    ];

    /**
     * Relación con la persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    /**
     * Relación con la categoría
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaPersona::class, 'id_categoria_persona');
    }

    /**
     * Relación con la comunidad (opcional)
     */
    public function comunidad(): BelongsTo
    {
        return $this->belongsTo(Comunidad::class, 'id_comunidad');
    }

    /**
     * Relación con el usuario que aprobó la regla
     */
    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Scope para reglas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('es_activo', true);
    }

    /**
     * Scope para un tipo específico de regla
     */
    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('tipo_regla', $tipo);
    }
}