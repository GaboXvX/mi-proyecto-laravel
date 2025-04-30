<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pregunta;
use App\Models\User;
use App\Models\RespuestaDeSeguridad;

class UserConf extends Component
{
    public $section = 'profile';

    public function setSection($section)
    {
        $this->section = $section;
    }

    public $preguntasDisponibles;
    public $usuario;
    public $preguntasUsuario;

    public function mount()
    {
        $this->preguntasDisponibles = Pregunta::all();
        $this->usuario = auth()->user();
        $this->preguntasUsuario = RespuestaDeSeguridad::where('id_usuario', $this->usuario->id_usuario)->get();
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