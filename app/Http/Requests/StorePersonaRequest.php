<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePersonaRequest extends FormRequest
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
            'cedula' => 'required|integer|unique:personas,cedula',
            'correo' => 'required|email|max:255|unique:personas,correo',
            'telefono' => 'required|digits:11',
            // 'estado' => 'required|max:13'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.integer' => 'La cédula debe ser un número entero.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe ser una dirección válida.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'telefono.required' => 'El número de teléfono es obligatorio.',
            'telefono.digits' => 'El número de teléfono debe tener exactamente 11 dígitos.',
            // 'estado.required' => 'El estado es obligatorio',
            // 'estado.max' => 'El estado debe contener máximo 8 caracteres',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => 'Error de validación',
            'messages' => $validator->errors()
        ], 422));
    }
}
