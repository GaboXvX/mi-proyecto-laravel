<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidenciaRequest extends FormRequest
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
            
            
            'tipo_incidencia' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1500',
            'nivel_prioridad' => 'required|integer', 
            'estado' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            
           
            
            'tipo_incidencia.required' => 'El tipo de incidencia es obligatorio.',
            'tipo_incidencia.string' => 'El tipo de incidencia debe ser una cadena de texto.',
            'tipo_incidencia.max' => 'El tipo de incidencia no debe exceder los 255 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no debe exceder los 1500 caracteres.',
            'nivel_prioridad.required' => 'El nivel de incidencia es obligatorio.',
            'nivel_prioridad.integer' => 'El nivel de incidencia debe ser un número entero.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.string' => 'El estado debe ser una cadena de texto.',
            'estado.max' => 'El estado no debe exceder los 255 caracteres.',
        ];
    }
}
