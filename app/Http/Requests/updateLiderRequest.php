<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateLiderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $slug = $this->route('slug');
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|integer|unique:lider_comunitario,cedula,' . $slug . ',slug',
            'correo' => 'required|email|max:255|unique:lider_comunitario,correo,' . $slug . ',slug',
            'telefono' => 'required|digits_between:10,15',
        ];
    }
    public function messages():array
    {
     return[  'nombre.required' => 'El nombre es obligatorio.',
     'apellido.required' => 'El apellido es obligatorio.',
     'cedula.required' => 'La cédula es obligatoria.',
     'cedula.integer' => 'La cédula debe ser un número entero.',
     'cedula.unique' => 'Esta cédula ya está registrada.',
     'correo.required' => 'El correo electrónico es obligatorio.',
     'correo.email' => 'El correo electrónico debe ser una dirección válida.',
     'correo.unique' => 'Este correo electrónico ya está registrado.',
     'telefono.required' => 'El número de teléfono es obligatorio.',
     'telefono.digits_between' => 'El número de teléfono debe tener entre 10 y 15 dígitos.',];   
    }
}
