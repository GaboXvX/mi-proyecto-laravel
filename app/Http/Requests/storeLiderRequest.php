<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeLiderRequest extends FormRequest
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
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|numeric|digits:8|unique:lider_comunitario,cedula',
            'correo' => 'required|email|max:255|unique:lider_comunitario,correo',
            'telefono' => 'nullable|numeric|digits_between:7,15',
        ];
    }
    public function messages(): array
    {
        return [
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'correo.unique' => 'Este correo ya está registrado.',
            'correo.email' => 'El correo debe ser una dirección de correo electrónico válida.',
        ];
    }
}
