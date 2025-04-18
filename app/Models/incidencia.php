<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class incidencia extends Model
{
    use HasFactory;
    protected $table = 'incidencias';
    protected $primaryKey = 'id_incidencia';
    protected $fillable = [
        'id_persona', 'id_lider', 'id_direccion', 'id_usuario', // Agregado id_usuario
        'slug',
        'tipo_incidencia',
        'descripcion',
        'nivel_prioridad',
        'estado',
        'created_at',
        'updated_at'
    ];
    public function getRouteKeyName() { 
        return 'slug'; 
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');  
    }
    public function lider()
    {
        return $this->belongsTo(lider_comunitario::class, 'id_lider');  
    }
    public function movimiento(){
        return $this->hasMany(movimiento::class,'id_incidencia');
    }
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
