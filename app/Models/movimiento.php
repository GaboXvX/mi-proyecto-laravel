<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class movimiento extends Model
{
    use HasFactory;
    protected $table='movimientos';
    protected $primaryKey='id_movimiento';

    public function lider(){
        return $this->belongsTo(Lider_Comunitario::class,'id_lider');
    }
    public function persona(){
        return $this->belongsTo(persona::class,'id_persona');
    }
    public function usuario(){
        return $this->belongsTo(User::class,'id_usuario');
    }
    public function incidencia(){
        return $this->belongsTo(incidencia::class,'id_incidencia');
    }
}

