<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use App\Models\RespuestaDeSeguridad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class seguridadController extends Controller
{
    // Mostrar las preguntas de seguridad para que el usuario las conteste
    public function show()
    {
        $usuario = Auth::user();

        // Verificar si ya tiene respuestas de seguridad
        if ($usuario->respuestasDeSeguridad->isNotEmpty()) {
            return redirect()->route('home')->with('success', 'Ya has registrado tus respuestas de seguridad.');
        }

        // Obtener las preguntas de seguridad
        $preguntas = Pregunta::all();

        return view('respuesta_seguridad', compact('preguntas'));
    }

    // Guardar las respuestas de seguridad
    public function store(Request $request)
    {
        $request->validate([
            'respuestas' => 'required|array',
            'respuestas.*' => 'required|string',
        ]);

        $usuario = Auth::user();

        // Registrar las respuestas de seguridad
        foreach ($request->input('respuestas') as $pregunta_id => $respuesta) {
            RespuestaDeSeguridad::create([
                'usuario_id' => $usuario->id_usuario,
                'pregunta_id' => $pregunta_id,
                'respuesta' => $respuesta,
            ]);
        }

        return redirect()->route('home')->with('success', 'Respuestas de seguridad registradas correctamente.');
    }
}
