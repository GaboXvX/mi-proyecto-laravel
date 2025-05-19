<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pregunta;
use App\Models\User;
use App\Models\RespuestaDeSeguridad;

class UserConf extends Component
{
    public $section = 'profile';
    public $email;
    public $nombre_usuario;
    public $contraseña;
    public $isDirty = false;

    public $preguntasDisponibles;
    public $usuario;
    public $preguntasUsuario;

    public function mount()
    {
        $this->preguntasDisponibles = Pregunta::all();
        $this->usuario = auth()->user();
        $this->preguntasUsuario = RespuestaDeSeguridad::where('id_usuario', $this->usuario->id_usuario)->get();
        
        // Inicializar valores del formulario
        $this->email = $this->usuario->email;
        $this->nombre_usuario = $this->usuario->nombre_usuario;
    }

    public function updated($propertyName)
    {
        // Verificar si hay cambios en los campos
        $this->isDirty = $this->email !== $this->usuario->email || 
                        $this->nombre_usuario !== $this->usuario->nombre_usuario ||
                        !empty($this->contraseña);
    }

    public function saveChanges()
    {
        // Validación
        $this->validate([
            'email' => 'required|email|unique:users,email,'.$this->usuario->id_usuario.',id_usuario',
            'nombre_usuario' => 'required|unique:users,nombre_usuario,'.$this->usuario->id_usuario.',id_usuario',
            'contraseña' => 'nullable|min:6'
        ]);

        // Actualizar usuario
        $user = User::find($this->usuario->id_usuario);
        $user->email = $this->email;
        $user->nombre_usuario = $this->nombre_usuario;
        
        if (!empty($this->contraseña)) {
            $user->password = bcrypt($this->contraseña);
        }
        
        $user->save();

        $this->isDirty = false;
        session()->flash('message', 'Cambios guardados correctamente');
    }

    public function setSection($section)
    {
        $this->section = $section;
    }

    public function render()
    {
        return view('livewire.userConf', [
            'preguntasDisponibles' => $this->preguntasDisponibles,
            'usuario' => $this->usuario,
            'preguntasUsuario' => $this->preguntasUsuario
        ]);
    }
}