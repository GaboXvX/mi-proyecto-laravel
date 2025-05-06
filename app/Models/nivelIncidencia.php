<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nivelIncidencia extends Model
{
    use HasFactory;
    protected $table = 'niveles_incidencias';
    protected $primaryKey = 'id_nivel_incidencia';
    protected $fillable = [
        'nivel',
        'nombre',
        'descripcion',
        'horas_vencimiento',
        'frecuencia_recordatorio',
        'color',
        'activo'
    ];
    /**
     * Relación uno a muchos con Incidencia.
     */
    public function incidencias()
    {
        return $this->hasMany(incidencia::class, 'id_nivel_incidencia', 'id_nivel_incidencia');
    }
}
