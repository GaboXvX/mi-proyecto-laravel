<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';

    protected $fillable = [
        'id_usuario',
        'id_persona',
        'id_incidencia',
        'tipo_notificacion',
        'titulo',
        'mensaje',
        'leido',
        'oculta',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    // Relación con persona (si aplica)
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    // Relación con incidencia (si aplica)
    public function incidencia()
    {
        return $this->belongsTo(Incidencia::class, 'id_incidencia', 'id_incidencia');
    }
}