<?php
// app/Models/Parroquia.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parroquia extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_parroquia';
    public function urbanizaciones()
    {
        return $this->hasMany(Urbanizacion::class, 'id_parroquia');
    }
    public function direccion()
    {
        return $this->hasOne(Direccion::class, 'id_sector');
    }
}

