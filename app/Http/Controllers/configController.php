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
            'contraseña' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('usuarios.configuracion', $usuario->id_usuario)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $usuario->nombre_usuario = $request->nombre_usuario;
            $usuario->email = $request->email;

            if ($request->filled('contraseña')) {
                $usuario->password = Hash::make($request->contraseña);
            }

            $usuario->save();

            // Registrar movimiento
            Movimiento::create([
                'id_usuario' => auth()->user()->id_usuario,
                'id_usuario_afectado' => $usuario->id_usuario,
                'descripcion' => 'Actualización de configuración de usuario'
            ]);

            return redirect()
                ->route('usuarios.configuracion', $usuario->id_usuario)
                ->with('success', 'Configuración actualizada correctamente');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('usuarios.configuracion', $usuario->id_usuario)
                ->with('error', 'Error al actualizar la configuración: ' . $e->getMessage());
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
            return redirect()
                ->route('usuarios.configuracion', $usuario->id_usuario)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::transaction(function () use ($usuario, $request) {
                // Eliminar respuestas antiguas
                $usuario->respuestas_de_seguridad()->delete();

                // Crear nuevas respuestas
                for ($i = 1; $i <= 3; $i++) {
                    RespuestaDeSeguridad::create([
                        'id_usuario' => $usuario->id_usuario,
                        'id_pregunta' => $request->input('pregunta_' . $i),
                        'respuesta' => $request->input('respuesta_' . $i)
                    ]);
                }

                // Registrar movimiento
                Movimiento::create([
                    'id_usuario' => auth()->user()->id_usuario,
                    'id_usuario_afectado' => $usuario->id_usuario,
                    'descripcion' => 'Cambio de preguntas de seguridad'
                ]);
            });

            return redirect()
                ->route('usuarios.configuracion', $usuario->id_usuario)
                ->with('success', 'Preguntas de seguridad actualizadas correctamente');
                
        } catch (\Exception $e) {
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