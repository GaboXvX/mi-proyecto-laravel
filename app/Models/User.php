<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\EmpleadoAutorizado;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_empleado_autorizado',
        'id_rol',
        'nombre_usuario',
        'email',
        'password',
        'id_estado_usuario',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
         'intentos_renovacion' => 'integer',
    'ultima_renovacion_en' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            $user->slug = Str::slug($user->nombre_usuario);

            // Validar unicidad del correo excluyendo el usuario actual
            if (\App\Models\User::where('email', $user->email)
                ->where('id_usuario', '!=', $user->id_usuario)
                ->exists()) {
                throw new \Exception('El correo ya está en uso.');
            }

            // Validar unicidad del nombre de usuario excluyendo el usuario actual
            if (\App\Models\User::where('nombre_usuario', $user->nombre_usuario)
                ->where('id_usuario', '!=', $user->id_usuario)
                ->exists()) {
                throw new \Exception('El nombre de usuario ya está en uso.');
            }
        });
    }

   

    public function lideres()
    {
        return $this->hasMany(Lider_Comunitario::class, 'id_usuario');
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

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'id_usuario', 'id_usuario'); // Relación con el modelo Incidencia
    }

    public function empleadoAutorizado()
    {
        return $this->belongsTo(EmpleadoAutorizado::class, 'id_empleado_autorizado'); // Relación con el modelo EmpleadoAutorizado
    }
    // app/Models/User.php


// app/Models/User.php
public function notificaciones()
{
    return $this->belongsToMany(Notificacion::class, 'notificaciones_usuarios', 'id_usuario', 'id_notificacion')
                ->withPivot('leido', 'fecha_leido', 'created_at', 'updated_at')
                ->orderBy('notificaciones.created_at', 'desc'); // Cambiar aquí si es necesario
}


public function notificacionesNoLeidas()
{
    return $this->notificaciones()->wherePivot('leido', false);
}
public function notificacionesUsuario()
{
    return $this->belongsToMany(Notificacion::class, 'notificacion_usuario', 'id_usuario', 'id_notificacion')
                ->withPivot('leido', 'fecha_leido')
                ->withTimestamps();
}
public function puedeRenovar()
{
    // Solo puede renovar si está rechazado (id_estado_usuario == 4)
    // y no ha excedido el límite de 3 intentos
    return $this->id_estado_usuario == 4 && 
           $this->intentos_renovacion < 3;
}

public function incrementarIntentosRenovacion()
{
    $this->increment('intentos_renovacion');
    $this->ultima_renovacion_en = now();
    $this->save();
}
}