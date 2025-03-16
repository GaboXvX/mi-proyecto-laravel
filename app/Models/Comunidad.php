<?php

// app/Models/Comunidad.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comunidad extends Model
{
    use HasFactory;
    protected $primaryKey ='id_comunidad';
    protected $table='comunidades';
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'id_sector');
    }

public function lideres_comunitarios()
{
    return $this->hasMany(Lider_Comunitario::class, 'id_comunidad');
}

    public function direccion()
    {
        return $this->hasMany(Comunidad::class, 'id_comunidad');
    }
}

