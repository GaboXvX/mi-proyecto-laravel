<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use App\Models\RespuestaDeSeguridad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class RecuperarController extends Controller
{
    // Método para mostrar la vista de ingresar la cédula
    public function ingresarCedula()
    {
        return view('recuperar.ingresarCedula');
    }

    // Método para procesar el formulario con la cédula
    public function procesarFormulario(Request $request)
    {
        $request->validate([
            'cedula' => 'required|numeric',
        ]);

        // Buscar el usuario por la cédula
        $usuario = User::where('cedula', $request->cedula)->first();

        if (!$usuario) {
            return redirect()->back()->withErrors(['cedula' => 'No se encontró un usuario con esa cédula.']);
        }

        // Verificar si el usuario está activo (estado 1)
        if ($usuario->id_estado_usuario != 1) {
            return redirect()->back()->withErrors(['estado' => 'El usuario no está activo y no puede recuperar la contraseña.']);
        }

        // Obtener las respuestas de seguridad del usuario
        $respuestasDeSeguridad = RespuestaDeSeguridad::where('id_usuario', $usuario->id_usuario)
                                                     ->with('pregunta')
                                                     ->get();

        if ($respuestasDeSeguridad->isEmpty()) {
            return redirect()->back()->withErrors(['cedula' => 'El usuario no tiene preguntas de seguridad configuradas.']);
        }

        // Seleccionar una pregunta aleatoria
        $preguntaAleatoria = $respuestasDeSeguridad->random();

        // Pasar los datos a la vista de recuperarClave
        return view('recuperar.recuperarClave', [
            'usuario' => $usuario,
            'pregunta' => $preguntaAleatoria->pregunta,
            'respuesta' => $preguntaAleatoria->respuesta
        ])->with('success', 'Pregunta seleccionada correctamente. Ahora, por favor, responde a la pregunta de seguridad.');
    }

    // Método para validar la respuesta ingresada por el usuario
    public function validarRespuesta(Request $request)
    {
        $request->validate([
            'respuesta' => 'required|string',
            'usuario_id' => 'required|exists:users,id_usuario',
            'pregunta_id' => 'required|exists:preguntas_de_seguridad,id_pregunta',
        ]);

        $respuestaCorrecta = RespuestaDeSeguridad::where('id_usuario', $request->usuario_id)
                                                  ->where('id_pregunta', $request->pregunta_id)
                                                  ->value('respuesta');

        if ($respuestaCorrecta && $respuestaCorrecta === $request->respuesta) {
            $token = Str::random(40);
            session(['token_cambiar_clave' => $token, 'usuario_validado' => $request->usuario_id]);

            return response()->json([
                'success' => true,
                'message' => 'Respuesta correcta. Redirigiendo...',
                'redirect_url' => route('cambiar-clave', ['token' => $token])
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Respuesta incorrecta. Inténtelo de nuevo.'
            ]);
        }
    }

    // Método para mostrar la vista de cambiar contraseña
    public function mostrarCambioClave(Request $request, $token)
    {
        // Validar el token
        if ($token !== session('token_cambiar_clave')) {
            return redirect()->route('recuperar.ingresarCedula')->with('error', 'Acceso no autorizado.');
        }

        // Obtener el usuario por su ID almacenado en la sesión
        $usuario = User::findOrFail(session('usuario_validado'));

        return view('recuperar.cambiar-contraseña', ['usuario' => $usuario]);
    }

    // Método para actualizar la contraseña
    public function update(Request $request, $usuarioId)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $usuario = User::findOrFail($usuarioId);
        $usuario->password = Hash::make($request->password);
        $usuario->save();

        // Limpiar la sesión después de cambiar la contraseña
        session()->forget(['token_cambiar_clave', 'usuario_validado']);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente. Ahora puedes iniciar sesión.',
            'redirect_url' => route('login'),
        ]);
    }
}