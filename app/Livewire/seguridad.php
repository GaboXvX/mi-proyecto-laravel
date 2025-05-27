<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Seguridad extends Component
{
    public $email;
    public $username;
    public $password_actual;
    public $password_nueva;
    public $password_confirmar;

    public function mount()
    {
        $user = Auth::user();
        $this->email = $user->email;
        $this->username = $user->username;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
    }

    protected function rules()
    {
        return [
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'username' => 'required|string|unique:users,username,' . Auth::id(),
            'password_nueva' => 'nullable|min:8|same:password_confirmar',
            'password_confirmar' => 'nullable|min:8',
        ];
    }

    public function guardarCambios()
    {
        $this->validate();

        $user = Auth::user();

        // Verifica si cambió email o username
        $user->email = $this->email;
        $user->username = $this->username;

        // Si quiere cambiar la contraseña
        if ($this->password_nueva) {
            if (!Hash::check($this->password_actual, $user->password)) {
                $this->addError('password_actual', 'La contraseña actual no es correcta.');
                return;
            }

            $user->password = Hash::make($this->password_nueva);
        }

        $user->save();

        session()->flash('message', 'Cambios guardados correctamente.');

        // Limpiar campos de contraseña
        $this->password_actual = $this->password_nueva = $this->password_confirmar = null;
    }

    public function render()
    {
        return view('livewire.seguridad');
    }
}
