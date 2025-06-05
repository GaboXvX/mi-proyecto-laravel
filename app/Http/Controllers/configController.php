<?php

namespace App\Http\Controllers;

use App\Models\movimiento;
use App\Models\pregunta;
use App\Models\RespuestaDeSeguridad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Support\Facades\Log;

class configController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        $empleadoAutorizado = $usuario->empleadoAutorizado;
        $cargo = $empleadoAutorizado->cargo;
        $preguntasUsuario = $usuario->respuestas_de_seguridad()->with('pregunta')->get();
        $preguntasDisponibles = pregunta::all();
        
        return view('usuarios.configuracion', compact('usuario', 'empleadoAutorizado', 'cargo', 'preguntasUsuario', 'preguntasDisponibles'));
    }

    public function actualizar(Request $request, $id_usuario)
{
    $usuario = User::findOrFail($id_usuario);

    $validator = Validator::make($request->all(), [
        'nombre_usuario' => [
            'required',
            'string',
            'max:255',
            Rule::unique('users')->ignore($usuario->id_usuario, 'id_usuario')
        ],
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users')->ignore($usuario->id_usuario, 'id_usuario')
        ],
        'contraseña' => [
            'nullable',
            'string',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
        ],
    ], [
        'contraseña.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial.',
        'contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'contraseña.confirmed' => 'Las contraseñas no coinciden.'
    ]);

    if ($validator->fails()) {
        return redirect()
            ->route('usuarios.configuracion', $usuario->id_usuario)
            ->withErrors($validator)
            ->withInput()
            ->with('error_type', 'password_validation');
    }

    try {
        DB::beginTransaction();

        $usuario->nombre_usuario = $request->nombre_usuario;
        $usuario->email = $request->email;

        $changes = [];
        $passwordChanged = false;

        if ($request->filled('contraseña')) {
            if (Hash::check($request->contraseña, $usuario->password)) {
                return redirect()
                    ->route('usuarios.configuracion', $usuario->id_usuario)
                    ->with('warning', 'La nueva contraseña no puede ser igual a la actual.')
                    ->with('error_type', 'same_password');
            }

            $usuario->password = Hash::make($request->contraseña);
            $passwordChanged = true;
            $changes[] = 'contraseña';
        }

        if ($usuario->isDirty('email')) {
            $changes[] = 'email';
        }

        if ($usuario->isDirty('nombre_usuario')) {
            $changes[] = 'nombre de usuario';
        }

        $usuario->save();

        // Registrar movimiento
        $descripcion = 'Actualización de configuración: '.implode(', ', $changes);
        
        if ($passwordChanged) {
            $descripcion .= ' (Cambio de contraseña)';
            
            Movimiento::create([
                'id_usuario' => auth()->id(),
                'id_usuario_afectado' => $usuario->id_usuario,
                'descripcion' => 'Cambio de contraseña exitoso'
            ]);
            
            // Opcional: Registrar en log en lugar de enviar email
            Log::info("Usuario {$usuario->id_usuario} cambió su contraseña");
        }

        Movimiento::create([
            'id_usuario' => auth()->id(),
            'id_usuario_afectado' => $usuario->id_usuario,
            'descripcion' => $descripcion
        ]);

        DB::commit();

        $message = 'Configuración actualizada correctamente';
        if ($passwordChanged) {
            $message .= '. Tu contraseña ha sido cambiada exitosamente.';
        }

        return redirect()
            ->route('usuarios.configuracion')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()
            ->route('usuarios.configuracion', $usuario->id_usuario)
            ->with('error', 'Error al actualizar la configuración: '.$e->getMessage())
            ->with('error_type', 'general_error');
    }
}

    public function cambiarPreguntas(Request $request, $id_usuario)
{
    $usuario = User::findOrFail($id_usuario);

    $validator = Validator::make($request->all(), [
        'pregunta_1' => 'required|different:pregunta_2,pregunta_3|exists:preguntas_de_seguridad,id_pregunta',
        'pregunta_2' => 'required|different:pregunta_1,pregunta_3|exists:preguntas_de_seguridad,id_pregunta',
        'pregunta_3' => 'required|different:pregunta_1,pregunta_2|exists:preguntas_de_seguridad,id_pregunta',
        'respuesta_1' => 'required|string|max:255',
        'respuesta_2' => 'required|string|max:255',
        'respuesta_3' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        return redirect()
            ->route('usuarios.configuracion', $usuario->id_usuario)
            ->withErrors($validator)
            ->withInput();
    }

    try {
        DB::transaction(function () use ($usuario, $request) {
            // Eliminar respuestas antiguas
            $usuario->respuestas_de_seguridad()->delete();

            // Crear nuevas respuestas (hasheadas)
            for ($i = 1; $i <= 3; $i++) {
                RespuestaDeSeguridad::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_pregunta' => $request->input('pregunta_' . $i),
                    'respuesta' => Hash::make($request->input('respuesta_' . $i))
                ]);
            }

            // Registrar movimiento
            Movimiento::create([
                'id_usuario' => auth()->user()->id_usuario,
                'id_usuario_afectado' => $usuario->id_usuario,
                'descripcion' => 'Cambio de preguntas de seguridad'
            ]);
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Preguntas de seguridad actualizadas correctamente'
            ]);
        }
        
        return redirect()
            ->route('usuarios.configuracion', $usuario->id_usuario)
            ->with('success', 'Preguntas de seguridad actualizadas correctamente');
            
    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar las preguntas de seguridad: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()
            ->route('usuarios.configuracion', $usuario->id_usuario)
            ->with('error', 'Error al cambiar las preguntas de seguridad: ' . $e->getMessage());
    }
}


    public function restaurar($id_usuario)
    {
        try {
            $usuario = User::where('id_usuario', $id_usuario)->first();
            $usuario->password = bcrypt($usuario->cedula);
            $usuario->save();
            $movimiento = new movimiento();
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->id_usuario_afectado = $usuario->id_usuario;
            $movimiento->descripcion = 'se restauro el usuario ';
            $movimiento->save();
            return redirect()->route('usuarios.index')->with('success', 'usuario restaurado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al restaurar el usuario: ' . $e->getMessage());
        }
    }
}