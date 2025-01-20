<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direccion';
    protected $primaryKey = 'id_direccion';

    protected $fillable = [
        'estado', 
        'municipio', 
        'comunidad', 
        'sector',
    ];

    
    public function persona()
    {
        return $this->hasMany(persona::class, 'id_direccion');
    }
    
    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class, 'id_comunidad');
    }
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'id_sector');
    }
    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'id_parroquia');
    }
    public function urbanizacion()
    {
        return $this->belongsTo(Urbanizacion::class, 'id_urbanizacion');
    }
    public function lider(){
        return $this->hasOne(Lider_Comunitario::class,'id_direccion');
    }
}
