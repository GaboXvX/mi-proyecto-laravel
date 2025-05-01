<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class movimiento extends Model
{
    use HasFactory;
    protected $table='movimientos';
    protected $primaryKey='id_movimiento';
    protected $fillable = [
        'id_usuario',
        'id_direccion',
        'id_incidencia',
        'id_persona',
        'id_usuario_afectado',
        'descripcion',
        // Agrega aquí otros campos que necesites asignar masivamente
    ];
    
    public function persona(){
        return $this->belongsTo(persona::class,'id_persona');
    }
    public function usuario(){
        return $this->belongsTo(User::class,'id_usuario');
    }
    public function incidencia(){
        return $this->belongsTo(incidencia::class,'id_incidencia');
    }
    public function usuarioAfectado()
    {
        return $this->belongsTo(User::class, 'id_usuario_afectado');
    }
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id_direccion');
    }

    // Relación con la incidencia afectada (si aplica)
    
}

