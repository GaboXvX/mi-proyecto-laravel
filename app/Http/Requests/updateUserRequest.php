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
            'email' => 'required|email|max:255|unique:users,email,' . $id_usuario . ',id_usuario',
            'nombre_usuario' => 'required|string|max:255',
            'contraseña' => 'nullable|min:8', // Contraseña opcional pero con longitud mínima
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
            'contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
