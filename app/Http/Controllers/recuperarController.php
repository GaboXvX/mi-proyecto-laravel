<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use App\Models\RespuestaDeSeguridad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            // Si el usuario no está activo, redirigir con un mensaje de error
            return redirect()->back()->withErrors(['estado' => 'El usuario no está activo y no puede recuperar la contraseña.']);
        }

        // Obtener las respuestas de seguridad del usuario
        $respuestasDeSeguridad = RespuestaDeSeguridad::where('id_usuario', $usuario->id_usuario)
                                                     ->with('pregunta')
                                                     ->get();

        // Verificar si el usuario tiene preguntas de seguridad configuradas
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

    // Método para mostrar la vista con la pregunta de seguridad
    public function recuperarClave($usuarioId, $preguntaId)
    {
        // Obtener usuario y pregunta de la base de datos
        $usuario = User::find($usuarioId);
        $pregunta = Pregunta::find($preguntaId);

        // Verificar que la pregunta y el usuario existen
        if (!$usuario || !$pregunta) {
            return redirect()->route('recuperar.ingresarCedula')->withErrors(['error' => 'Usuario o pregunta no encontrados.']);
        }

        return view('recuperar.recuperarClave', [
            'usuario' => $usuario,
            'pregunta' => $pregunta,
        ]);
    }

    // Método para validar la respuesta ingresada por el usuario
    public function validarRespuesta(Request $request)
    {
        // Validar que la respuesta esté presente
        $request->validate([
            'respuesta' => 'required|string',
        ]);

        // Obtener la respuesta correcta de la base de datos usando el id_usuario y id_pregunta
        $respuestaCorrecta = RespuestaDeSeguridad::where('id_usuario', $request->usuario_id)
                                                  ->where('id_pregunta', $request->pregunta_id)
                                                  ->value('respuesta');

        // Verificar si la respuesta es correcta
        if ($respuestaCorrecta && $respuestaCorrecta === $request->respuesta) {
            // Redirigir al cambio de clave si la respuesta es correcta
            return redirect()->route('cambiar-clave', ['usuarioId' => $request->usuario_id])
            ->with('success', 'Respuesta correcta. Ahora puede cambiar su clave.');
   } else {
            // Si la respuesta es incorrecta, redirigir de nuevo con un error
            return redirect()->route('recuperar.recuperarClave', [
                'usuarioId' => $request->usuario_id,
                'preguntaId' => $request->pregunta_id
            ])->withErrors(['respuesta' => 'Respuesta incorrecta. Inténtelo de nuevo.'])->withInput();
        }
    }

    public function mostrarCambioClave($usuarioId)
    {
        // Obtener el usuario por su ID
        $usuario = User::findOrFail($usuarioId);

        // Verificar si el usuario existe
        return view('recuperar.cambiar-contraseña', ['usuario' => $usuario]);
    }

    public function update(Request $request, $usuarioId)
    {
        // Validar los datos enviados
        $request->validate([
            'password' => 'required|string|min:8|confirmed',  // Validar la contraseña con confirmación
        ]);

        // Recuperar al usuario usando el ID proporcionado
        $usuario = User::findOrFail($usuarioId);

        // Cambiar la contraseña del usuario
        $usuario->password = Hash::make($request->password); // Usar Hash para almacenar la contraseña de manera segura

        // Guardar el usuario con la nueva contraseña
        $usuario->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('login')->with('success', 'Contraseña actualizada correctamente. Ahora puedes iniciar sesión.');
    }

}
