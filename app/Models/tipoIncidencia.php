<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipoIncidencia extends Model
{
    use HasFactory;
    protected $table = 'tipos_incidencias';
    protected $primaryKey = 'id_tipo_incidencia';
    protected $fillable = [
        'nombre',
    ];
    public $timestamps = true;
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'id_tipo_incidencia', 'id_tipo_incidencia');
    }
}
