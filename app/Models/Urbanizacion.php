<?php
// app/Models/Urbanizacion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Urbanizacion extends Model
{
    use HasFactory;
    protected $primaryKey ='id_urbanizacion';
    protected $table ='urbanizaciones';
    protected $fillable = ['nombre', 'id_parroquia'];

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'id_parroquia');
    }

    public function sectores()
    {
        return $this->hasMany(Sector::class, 'id_urbanizacion');
    }
    public function direccion()
    {
        return $this->hasMany(direccionIncidencia::class,'id_urbanizacion');
    }
}

