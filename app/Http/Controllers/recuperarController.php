<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use App\Models\RespuestaDeSeguridad;
use App\Models\User;
use App\Models\EmpleadoAutorizado; // Importar el modelo de empleados autorizados
use App\Models\movimiento;
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

        // Buscar el empleado autorizado por la cédula
        $empleado = EmpleadoAutorizado::where('cedula', $request->cedula)->first();

        if (!$empleado) {
            return redirect()->back()->withErrors(['cedula' => 'No se encontró un empleado con esa cédula.']);
        }

        // Buscar el usuario asociado al empleado autorizado
        $usuario = User::where('id_empleado_autorizado', $empleado->id_empleado_autorizado)->first();

        if (!$usuario) {
            return redirect()->back()->withErrors(['cedula' => 'No se encontró un usuario asociado a este empleado.']);
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

        $respuestaHash = \App\Models\RespuestaDeSeguridad::where('id_usuario', $request->usuario_id)
            ->where('id_pregunta', $request->pregunta_id)
            ->value('respuesta');

        if ($respuestaHash && \Illuminate\Support\Facades\Hash::check($request->respuesta, $respuestaHash)) {
            $token = \Illuminate\Support\Str::random(40);
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
    $validator = Validator::make($request->all(), [
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
        ], 422);
    }

    $usuario = User::findOrFail($usuarioId);
    $usuario->password = Hash::make($request->password);
    $usuario->save();

    // Registrar movimiento
    $movimiento = new movimiento();
    $movimiento->id_usuario = $usuario->id_usuario; // Usar el mismo usuario que cambió la contraseña
    $movimiento->descripcion = 'Se actualizó la contraseña';
    $movimiento->save();

    // Limpiar la sesión
    session()->forget(['token_cambiar_clave', 'usuario_validado']);

    return response()->json([
        'success' => true,
        'message' => 'Contraseña actualizada correctamente. Ahora puedes iniciar sesión.',
        'redirect_url' => route('login'),
    ]);
}

public function actualizarCorreo(Request $request, $usuarioId)
{
    $usuario = User::findOrFail($usuarioId);

    $validator = Validator::make($request->all(), [
        'email' => [
            'required',
            'email',
            'confirmed',
            'unique:users,email,'.$usuarioId.',id_usuario',
            function ($attribute, $value, $fail) use ($usuario) {
                if ($value === $usuario->email) {
                    $fail('El correo ingresado es el mismo que el actual.');
                }
            },
        ],
    ], [
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El correo electrónico debe ser válido.',
        'email.confirmed' => 'Los correos electrónicos no coinciden.',
        'email.unique' => 'El correo electrónico ya está en uso.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $usuario->email = $request->email;
    $usuario->save();

    // Registrar movimiento
    $movimiento = new movimiento();
    $movimiento->id_usuario = $usuario->id_usuario;
    $movimiento->descripcion = 'Se actualizó el correo electrónico';
    $movimiento->save();

    return response()->json([
        'success' => true,
        'message' => 'Correo electrónico actualizado correctamente.',
        'redirect_url' => route('login'),
    ]);
}

}