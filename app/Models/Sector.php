<?php

// app/Models/Sector.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_sector';
    protected $table ='sectores';
    public function urbanizacion()
    {
        return $this->belongsTo(Urbanizacion::class, 'id_urbanizacion');
    }

    public function comunidad()
    {
        return $this->hasMany(Comunidad::class, 'id_sector');
    }
    public function direccion()
    {
        return $this->hasMany(Direccion::class, 'id_sector');
    }
}


