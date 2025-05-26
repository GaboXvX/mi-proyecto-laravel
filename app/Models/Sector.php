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
    protected $fillable = ['nombre', 'id_urbanizacion'];

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
        return $this->hasMany(direccionIncidencia::class, 'id_sector');
    }
}


