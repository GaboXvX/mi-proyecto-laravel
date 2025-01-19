<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storePeticionRequest extends FormRequest
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
<<<<<<< HEAD
           
           
=======
            'cedula' => 'required|integer|unique:peticiones',  
            'correo' => 'required|email|max:255|unique:peticiones',  
            'telefono' => 'required|digits_between:10,15',
>>>>>>> 6274081162731933fa5a1f461cf7cde9adc29d56
        ];
    }
    public function messages(): array
    {
        return[ 'nombre.required' => 'El nombre es obligatorio.',
        'apellido.required' => 'El apellido es obligatorio.',
<<<<<<< HEAD
       
    ];
=======
        'cedula.required' => 'La cédula es obligatoria.',
        'cedula.integer' => 'La cédula debe ser un número entero.',
        'cedula.unique' => 'Esta cédula ya está registrada.',
        'correo.required' => 'El correo electrónico es obligatorio.',
        'correo.email' => 'El correo electrónico debe ser una dirección válida.',
        'correo.unique' => 'Este correo electrónico ya está registrado.',
        'telefono.required' => 'El número de teléfono es obligatorio.',
        'telefono.digits_between' => 'El número de teléfono debe tener entre 10 y 15 dígitos.',];
>>>>>>> 6274081162731933fa5a1f461cf7cde9adc29d56
    }
}
