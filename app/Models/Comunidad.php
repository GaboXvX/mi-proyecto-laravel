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
    protected $fillable = ['nombre', 'id_sector'];

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'id_sector');
    }



    public function direccion()
    {
        return $this->hasMany(Comunidad::class, 'id_comunidad');
    }
}

