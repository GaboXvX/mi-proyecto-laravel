<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id_usuario = $this->route('id_usuario');
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|integer|unique:users,cedula,' . $id_usuario . ',id_usuario',
            'email' => 'required|email|max:255|unique:users,email,' . $id_usuario . ',id_usuario',
            'contraseña' => 'required',
        ];
    }
    public function messages(): array
    {
        return[  'nombre.required' => 'El nombre es obligatorio.',
        'apellido.required' => 'El apellido es obligatorio.',
        'cedula.required' => 'La cédula es obligatoria.',
        'cedula.integer' => 'La cédula debe ser un número entero.',
        'cedula.unique' => 'Esta cédula ya está registrada.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El correo electrónico debe ser una dirección válida.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        'telefono.required' => 'El número de teléfono es obligatorio.',
        'telefono.digits_between' => 'El número de teléfono debe tener entre 10 y 15 dígitos.',
        'contraseña.required' => 'la contraseña es obligatoria'];
    }
}
