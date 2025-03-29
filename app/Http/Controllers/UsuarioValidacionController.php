<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsuarioValidacionController extends Controller
{
    public function validarUsuario(Request $request)
    {
        $cedula = $request->cedula;
        $nombre_usuario = $request->nombre_usuario;
        $email = $request->email;

        // Buscar en la tabla `users` con relación a `empleados_autorizados`
        $usuarioExistente = User::where('nombre_usuario', $nombre_usuario)
                                ->orWhere('email', $email)
                                ->orWhereHas('empleadoAutorizado', function ($query) use ($cedula) {
                                    $query->where('cedula', $cedula);
                                })
                                ->first();

        if ($usuarioExistente) {
            $errores = [];

            if ($usuarioExistente->nombre_usuario === $nombre_usuario) {
                $errores['nombre_usuario'] = "El nombre de usuario ya está en uso.";
            }
            if ($usuarioExistente->email === $email) {
                $errores['email'] = "El correo electrónico ya está registrado.";
            }
            if ($usuarioExistente->empleadoAutorizado && $usuarioExistente->empleadoAutorizado->cedula === $cedula) {
                $errores['cedula'] = "La cédula ya está registrada.";
            }

            return response()->json(['error' => true, 'errors' => $errores]);
        }

        return response()->json(['error' => false]);
    }
}
