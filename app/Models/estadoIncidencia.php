<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class estadoIncidencia extends Model
{
    use HasFactory;
    protected $table = 'estados_incidencias';
    protected $primaryKey = 'id_estado_incidencia';
    protected $fillable = [
        'nombre',
        'color',
    ];
    /**
     * Relación uno a muchos con Incidencia.
     */
    public function incidencias()
    {
        return $this->hasMany(incidencia::class, 'id_estado_incidencia', 'id_estado_incidencia');
    }
}
