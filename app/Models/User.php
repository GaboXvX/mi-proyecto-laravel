<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_usuario',
        'id_rol',    
        'nombre', 
        'email',
        'password',
        'slug',
        'apellido',
        'cedula',
        'nombre_usuario',
        'estado',
        'altura', 
        'fecha_nacimiento',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(roles::class, 'id_rol', 'id_rol'); 
    }

    public function lideres()
    {
        return $this->hasMany(Lider_Comunitario::class, 'id_usuario');
    }

    public function hasRole($role)
    {
        return $this->role->rol === $role;
    }

    public function personas()
    {
        return $this->hasMany(Persona::class, 'id_usuario');
    }

    public function movimiento()
    {
        return $this->hasMany(movimiento::class, 'id_usuario');
    }

    // Relación corregida: respuestas_de_seguridad
    public function respuestas_de_seguridad()
    {
        return $this->hasMany(RespuestaDeSeguridad::class, 'id_usuario');
    }
    // En el modelo User
public function estadoUsuario()
{
    return $this->belongsTo(EstadoUsuario::class, 'id_estado_usuario'); // Relación con EstadoUsuario
}

}
